<?php namespace App\Console\Commands;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Role;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use App\User;
use App\Facility;
use App\Actions;
use GuzzleHttp\Client as Guzzle;

class CERTSync extends Command
{

    /**
     * The console command name.
     *
     * @var $signature
     */
    protected $signature = 'CERTSync {--A|all : Process all users, including non-members}';

    private $ratings = [
        "Inactive"          => -2,
        "Suspended"         => -1,
        "Unknown"           => 0,
        "Pilot/Observer"    => 1,
        "Student"           => 2,
        "Student 2"         => 3,
        "Senior Student"    => 4,
        "Controller"        => 5,
        "Senior Controller" => 7,
        "Instructor"        => 8,
        "Senior Instructor" => 10,
        "Supervisor"        => 11,
        "Administrator"     => 12
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync our tables to CERT';

    public $log;

    private $guzzle;

    public function __construct()
    {
        parent::__construct();

        $this->guzzle = new Guzzle();
        $this->log = array();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        \DB::table("controllers")->update(["cert_update" => 0]);
        $log = ['deletes' => [], 'purges' => [], 'suspends' => []];

        $users = $this->option("all") ? User::all() : User::where('facility', '!=', 'ZZN');

        $i = 0;
        foreach ($users->get() as $user) {
            $isHome = $user->flag_homecontroller && $user->facility !== "ZZN";

            try {
                $i++;
                $response = $this->guzzle->get("https://api.vatsim.net/api/ratings/{$user->cid}");
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    if ($e->getResponse()->getStatusCode() == 404) {
                        if (!$isHome) {
                            $this->log[] = "Purging Non-member {$user->fullname()} ({$user->cid}).";
                            $this->line("Purging Non-member {$user->fullname()} ({$user->cid}).");
                        } else {
                            $this->log[] = "Purging home controller {$user->fullname()} ({$user->cid}) ({$user->facilityObj->id}).";
                            $this->line("Purging Home controller {$user->fullname()} ({$user->cid}) ({$user->facilityObj->id}).");
                        }
                        $log['purges'][] = $user->cid;
                        continue;
                    }
                }
                $this->error(\GuzzleHttp\Psr7\str($e->getRequest()) . "\n" . \GuzzleHttp\Psr7\str($e->getResponse()));
                $this->line("Error after $i");
                continue;
            }

            $apiUser = json_decode($response->getBody(), true);

            if ($apiUser['division'] !== "USA" && $isHome) {
                //Transfer Out
                $this->line("Deleting " . $user->fname . " " . $user->lname . " (" . $user->cid . ") (" . Helper::ratingShortFromInt($user->rating) . ") from {$user->facilityObj->id}");
                $log['deletes'][] = $user->cid;
            }
            if ($apiUser['rating'] < 0 && $apiUser['division'] === "USA") {
                //Suspended or Inactive
                $this->line("Deleting " . $user->fname . " " . $user->lname . " (" . $user->cid . ") (SUS/INAC) from {$user->facilityObj->id}");
                $log['suspends'][] = $user->cid;
            }
            //Process Preferred Name
            $updateName = true;
            if ($user && $user->prefname) {
                if (Carbon::now()->subDays(14)->greaterThanOrEqualTo($user->prefname_date)) {
                    //Expired
                    $user->prefname = 0;
                    $user->prefname_date = null;
                } else {
                    $updateName = false;
                }
            }
            if ($updateName) {
                $user->fname = ucfirst($apiUser['name_first']);
                $user->lname = ucwords($apiUser['name_last']);
            }
            $user->rating = $apiUser['rating'];
            $user->cert_update = 1;
            $user->save();
        }

        $count = 0;
        foreach ($log as $type) {
            $count += count($type);
        }

        if ($count > 800) {
            $this->log[] = "More than 800 records are going to be deleted... possible error. Aborting.";
            $this->error("More than 800 records are going to be deleted... possible error. Aborting.");

            EmailHelper::sendEmail("vatusa12@vatusa.net", "CERTSync Error", "emails.logsend", ['log' => $this->log]);
            exit;
        }

        $this->line("User collection complete. Processing deletions.");

        foreach ($log['deletes'] as $cid) {
            $delUser = User::find($cid);
            $facility = $delUser->facilityObj->id;
            $delUser->removeFromFacility("Automated", "Left division", "ZZN");
            $delUser->flag_homecontroller = 0;
            $delUser->save();
            $this->checkDeleted($delUser);
            $this->line("Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingShortFromInt($delUser->rating) . ") from $facility");
            $this->log[] = "Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . Helper::ratingShortFromInt($delUser->rating) . ") from $facility";
        }
        foreach ($log['suspends'] as $cid) {
            $delUser = User::find($cid);
            $facility = $delUser->facilityObj->id;
            $delUser->removeFromFacility("Automated", "Suspended/Inactive", "ZZN");
            $delUser->flag_homecontroller = 0;
            $delUser->save();
            $this->checkDeleted($delUser);

            /*$log = new Actions();
            $log->to = $delUser->cid;
            $log->log = "User suspended or inactive, removing from division";
            $log->save();*/

            $this->line("Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (Suspended/Inactive) from $facility");
            $this->log[] = "Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (Suspended/Inactive) from $facility";
        }
        foreach ($log['purges'] as $cid) {
            $purgeUser = User::find($cid);
            $facility = $purgeUser->facilityObj->id;
            $this->line("Purged " . $purgeUser->fname . " " . $purgeUser->lname . " (" . $purgeUser->cid . ") (" . Helper::ratingShortFromInt($purgeUser->rating) . ") from $facility");
            $this->log[] = "Purged " . $purgeUser->fname . " " . $purgeUser->lname . " (" . $purgeUser->cid . ") (" . Helper::ratingShortFromInt($purgeUser->rating) . ") from $facility";
            $purgeUser->purge();
        }

        $this->log[] = "";
        $this->log[] = "Transferred Out: " . number_format(count($log['deletes']));
        $this->log[] = "";
        $this->log[] = "Suspended/Inactive: " . number_format(count($log['suspends']));
        $this->log[] = "";
        $this->log[] = "Purged: " . number_format(count($log['purges']));
        $this->log[] = "";
        $this->log[] = "Home Controllers: " . number_format(User::where('facility',
                'NOT LIKE', "ZZN")->count());
        EmailHelper::sendEmail([
            "vatusa1@vatusa.net",
            "vatusa2@vatusa.net",
            //"vatusa12@vatusa.net",
        ], "CERT Sync", "emails.logsend", ['log' => $this->log]);

        $this->info("Completed in " . (microtime(true) - $start) . " seconds.");
    }

    public function checkDeleted($user)
    {
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
}
