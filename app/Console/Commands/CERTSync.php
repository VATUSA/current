<?php namespace App\Console\Commands;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Models\ExamResults;
use App\Models\Role;
use App\Models\Transfers;
use Carbon\Carbon;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Facility;
use App\Models\Actions;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CERTSync extends Command
{

    /**
     * The console command name.
     *
     * @var $signature
     */
    protected $signature = 'CERTSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync our tables to VATSIM';

    public $log;

    private $guzzle;

    public function __construct()
    {
        parent::__construct();

        $this->guzzle = new Guzzle();
        $this->log = array();
        
        ini_set('memory_limit', '1024M');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $start = microtime(true);
        $log = ['deletes' => [], 'suspends' => []];

        $roster = $this->fetchRoster(app()->environment("dev"));

        DB::table("controllers")->update(["cert_update" => 0]);

        $this->info("Processing user updates...");
        $this->log[] = "Processing user updates...";
        $count = 0;
        foreach ($roster as $page) {
            foreach ($page as $apiUser) {
                $cid = $apiUser['id'];
                $rating = $apiUser['rating'];
                $email = $apiUser['email'];
                $fname = ucfirst($apiUser['name_first']);
                $lname = ucwords($apiUser['name_last']);
                $user = User::find($cid);

                if ($user) {
                    $updateName = true;
                    if ($user->prefname) {
                        if (Carbon::now()->subDays(14)->greaterThanOrEqualTo($user->prefname_date)) {
                            //Expired
                            $user->prefname = 0;
                            $user->prefname_date = null;
                        } else {
                            $updateName = false;
                        }
                    }
                    if ($updateName) {
                        $user->fname = ucfirst(trim($fname));
                        $user->lname = ucwords(trim($lname));
                    }
                    $oldRating = $user->rating;
                    $user->rating = $rating;
                    $user->email = $email;
                    $user->save();

                    if (SMFHelper::isRegistered($cid)) {
                        SMFHelper::updateData($cid, $user->lname, $user->fname, $email);
                        SMFHelper::setPermissions($cid);
                    }

                    if ($user->rating <= 0) {
                        //Suspended or Inactive
                        if ($user->rating != $oldRating && $user->flag_homecontroller) {
                            $log['suspends'][] = $user->cid;
                        }
                    }

                    $user->cert_update = 1;
                    $user->save();
                    $count++;
                }
            }
        }


        $this->info("Users updated: " . number_format($count));
        $this->log[] = "Users updated: " . number_format($count);

        $this->info("User updates complete. Processing deletions.");
        $this->log[] = "User updates complete. Processing deletions.";
        foreach (User::where('cert_update', 0)->where('flag_homecontroller', 1)->get() as $out) {
            //Transferred out.
            //Verify with user endpoint.
            try {
                $response = $this->guzzle->get("https://api.vatsim.net/api/ratings/$out->cid/", [
                    'headers' => [
                        'Authorization' => 'Token ' . config('services.vatsim.apiToken')
                    ]
                ]);
                $div = json_decode($response->getBody(), true)['division'];
                if ($div === "USA") {
                    continue;
                }
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    if ($e->getCode() !== 404) {
                        $this->error($e->getResponse()->getBody());
                        continue;
                    }
                } else {
                    continue;
                }
            }
            $log['deletes'][] = $out->cid;
        }

        $deleteCount = count($log['deletes']) + count($log['suspends']);

        $this->info("Total to be deleted: " . number_format($deleteCount));
        $this->info[] = "Total to be deleted: " . number_format($deleteCount);

        if ($deleteCount > 800) {
            $this->log[] = "More than 800 records are going to be deleted... possible error. Aborting.";
            $this->error("More than 800 records are going to be deleted... possible error. Aborting.");

            EmailHelper::sendEmail("vatusa12@vatusa.net", "CERTSync Error", "emails.logsend",
                ['log' => $this->log]);
            exit;
        }

        foreach ($log['deletes'] as $cid) {
            $delUser = User::find($cid);
            $facility = $delUser->facilityObj->id;
            $delUser->removeFromFacility("Automated", "Left division", "ZZN");
            $this->checkDeleted($delUser);
            $this->line("Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingShortFromInt($delUser->rating) . ") from $facility");
            $this->log[] = "Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingShortFromInt($delUser->rating) . ") from $facility";
        }
        foreach ($log['suspends'] as $cid) {
            $delUser = User::find($cid);
            $facility = $delUser->facilityObj->id;
            $delUser->removeFromFacility("Automated", "Suspended/Inactive", "ZZN");
            $this->checkDeleted($delUser);

            /*$log = new Actions();
            $log->to = $delUser->cid;
            $log->log = "User suspended or inactive, removing from division";
            $log->save();*/

            $this->line("Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingLongFromInt($delUser->rating) . ") from $facility");
            $this->log[] = "Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingLongFromInt($delUser->rating) . ") from $facility";
        }
        /* foreach ($log['purges'] as $cid) {
             $purgeUser = User::find($cid);
             $facility = $purgeUser->facilityObj->id;
             $this->line("Purged " . $purgeUser->fname . " " . $purgeUser->lname . " (" . $purgeUser->cid . ") (" . Helper::ratingShortFromInt($purgeUser->rating) . ") from $facility");
             $this->log[] = "Purged " . $purgeUser->fname . " " . $purgeUser->lname . " (" . $purgeUser->cid . ") (" . Helper::ratingShortFromInt($purgeUser->rating) . ") from $facility";
             $purgeUser->purge();
         }*/

        $this->log[] = "";
        $this->log[] = "Transferred Out: " . number_format(count($log['deletes']));
        $this->info("Transferred Out: " . number_format(count($log['deletes'])));
        $this->log[] = "";
        $this->log[] = "Suspended/Inactive: " . number_format(count($log['suspends']));
        $this->info("Suspended/Inactive: " . number_format(count($log['suspends'])));
        $this->log[] = "";
        $this->log[] = "Home Controllers: " . number_format(User::where('facility',
                'NOT LIKE', "ZZN")->count());
        EmailHelper::sendEmail([
            "vatusa1@vatusa.net",
            "vatusa2@vatusa.net",
           // "vatusa12@vatusa.net",
        ], "CERT Sync", "emails.logsend", ['log' => $this->log]);

        $this->info("Completed in " . (microtime(true) - $start) . " seconds.");

        return 0;
    }

    public
    function checkDeleted(
        $user
    ) {
        $removals = "";
        if ($user->facility == "ZAE" || $user->facility == "ZZN") {
            return;
        }
        $fac = Facility::find($user->facility);
        if (RoleHelper::hasRole($user->cid, $user->facility, "ATM")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "ATM");
            $removals .= "Removed from ATM of " . $user->facility . "\n";
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "DATM")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "DATM");
            $removals .= "Removed from DATM of " . $user->facility . "\n";
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "TA")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "TA");
            $removals .= "Removed from TA of " . $user->facility . "\n";
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "EC")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "EC");
            $removals .= "Removed from EC of " . $user->facility . "\n";
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "FE")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "FE");
            $removals .= "Removed from FE of " . $user->facility . "\n";
        }
        if (RoleHelper::hasRole($user->cid, $user->facility, "WM")) {
            RoleHelper::deleteStaff($user->facility, $user->cid, "WM");
            $removals .= "Removed from WM of " . $user->facility . "\n";
        }
        foreach (Role::where('cid', $user->cid)->get() as $role) {
            $removals .= "Removed role " . $role->role . " for " . $role->facility . "\n";
            $role->delete();
        }

        /*if ($removals) {
            SMFHelper::createPost(7262, 82,
                "CERTSync: Staff deletion report for " . $user->fullname() . " (" . $user->cid . ")", $removals);
            $this->log[] = $removals;
        }*/
    }

    private function fetchRoster($testing = false): array
    {
        $this->info("Retrieving roster...");
        $this->log[] = "Retrieving roster...";

        $start = microtime(true);
        $roster = array();
        $perPage = 1000;
        $url = "https://api.vatsim.net/api/divisions/USA/members/?page_size=$perPage&paginated";

        if (Storage::exists('roster.json') && $testing) {
            //Use cached roster. Testing only.
            $this->line('Using cached roster.');
            $this->log[] = 'Using cached roster.';

            return json_decode(Storage::get('roster.json'), true);
        }

        try {
            $response = $this->guzzle->get($url, [
                'headers' => [
                    'Authorization' => 'Token ' . config('services.vatsim.apiToken')
                ]
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->error($e->getResponse()->getBody());
                exit(1);
            }
        }
        $response = json_decode($response->getBody(), true);
        $count = $response["count"];
        $recursiveCount = 0;
        $roster[] = $response["results"];
        $recursiveCount += count($response["results"]);

        $pages = ceil($count / $perPage);
        $promises = [];
        for ($i = 2; $i <= $pages; $i++) {
            $promises[] = $this->guzzle->getAsync($url . "&page=$i", [
                'headers' => [
                    'Authorization' => 'Token ' . config('services.vatsim.apiToken')
                ]
            ]);
        }
        try {
            $responses = Utils::settle($promises)->wait();
            $i = 2;
            foreach ($responses as $response) {
                if ($response['state'] !== "fulfilled") {
                    $this->error("$i Rejected");
                    exit(0);
                } else {
                    $rosterPage = json_decode($response['value']->getBody(), true)["results"];
                    $roster[] = $rosterPage;
                    $recursiveCount += count($rosterPage);
                }
                $i++;
            }
        } catch (ConnectException $e) {
            $this->error($e->getMessage());
            exit(1);
        }

        $this->info("Roster retrieved. Processed Pages: " . count($roster) . "/" . $pages);
        $this->log[] = "Roster retrieved. Processed Pages: " . count($roster) . "/" . $pages;

        $this->info("Retrieved Members: $recursiveCount/$count");
        $this->log[] = "Retrieved Members: $recursiveCount/$count";

        $this->info("Time: " . (microtime(true) - $start) . "s");
        $this->log[] = "Time: " . (microtime(true) - $start) . "s";

        if ($testing) {
            Storage::put('roster.json', json_encode($roster));
        }

        return $roster;
    }
}
