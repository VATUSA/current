<?php namespace App\Console\Commands;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Role;
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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime(true);
        $deleted = 0;
        \DB::table("controllers")->update(["cert_update" => 0]);
        $i = 0;
        $logIds = [];
        $purgeIds = [];
        $errorIds = [];
        $leftIds = [];

        $users = $this->option("all") ? User::all() : User::where('facility', '!=', 'ZZN');
        foreach ($users->get() as $user) {
            $retries = 0;
            $error = false;
            while (true) {
                if ($retries > 3) {
                    $this->error("3 consecutive errors occurred after $i. Aborting.");
                    exit(2);
                }
                try {
                    $i++;
                    $data = $this->guzzle->get("https://cert.vatsim.net/vatsimnet/idstatus.php?cid={$user->cid}");
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        if ($e->getResponse()->getStatusCode() == 404) {
                            if ($user->facility == "ZZN") {
                                $this->log[] = "Non-member {$user->fullname()} ({$user->cid}) no longer exists in VATUSA database. Deleting.";
                                // $this->line("Non-member {$user->fullname()} ({$user->cid}) no longer exists in VATUSA database. Deleting.");
                                $purgeIds[] = $user->cid;
                            } else {
                                $this->log[] = "Home controller {$user->fullname()} ({$user->cid}) no longer exists in VATUSA database. Deleting.";
                                // $this->line("Non-member {$user->fullname()} ({$user->cid}) no longer exists in VATUSA database. Deleting.");
                            }
                            continue 2;
                        } else {
                            $error = true;
                            // $this->error(\GuzzleHttp\Psr7\str($e->getRequest()) . "\n" . \GuzzleHttp\Psr7\str($e->getResponse()));
                            // $this->line("Error after $i");
                        }
                    } else {
                        $error = true;
                        // $this->error(\GuzzleHttp\Psr7\str($e->getRequest()) . "\n" . \GuzzleHttp\Psr7\str($e->getResponse()));
                        // $this->line("Error after $i");
                    }
                }

                if ($error) {
                    $retries++;
                    $error = false;
                    sleep(2);
                } else {
                    break;
                }
            }
            // $this->line($i);
            $xml = simplexml_load_string($data->getBody());
            $xmlUser = $xml->user[0];

            $lname = (string)$xmlUser->name_last ?? null;
            $fname = (string)$xmlUser->name_first ?? null;
            $rating = $this->ratings[(string)$xmlUser->rating] ?? 0;
            $division = (string)$xmlUser->division ?? null;

            if (is_null($lname) || is_null($fname) || is_null($division)) {
                $this->log[] = "XML Error. Skipping.";
                $this->line("XML Error. Skipping.");
                $errorIds[] = $user->cid;
                continue;
            }

            $user->lname = $lname;
            $user->fname = $fname;
            $user->rating = $rating;
            $user->save();

            if ($division !== "United States" && $user->flag_homecontroller) {
                $leftIds[] = $user->cid;
                continue;
            }
            if ($rating == -2) {
                if (!$user->flag_homecontroller) {
                    $this->log[] = "Non-member {$user->fullname()} ({$user->cid}) marked inactive by VATSIM. Deleting.";
                    // $this->line("Non-member {$user->fullname()} ({$user->cid}) marked inactive by VATSIM. Deleting.");
                    $purgeIds[] = $user->cid;
                } else {
                    $this->log[] = "Home controller {$user->fullname()} ({$user->cid}) marked inactive by VATSIM. Removing.";
                    // $this->line("Home controller {$user->fullname()} ({$user->cid}) marked inactive by VATSIM. Removing.");
                    $leftIds[] = $user->cid;
                }
                continue;
            }
            if ($rating >= 0) {
                $user->cert_update = 1;
                $user->save();
            } else {
                // Suspended
                $log = new Actions();
                $log->to = $user->cid;
                $log->log = "User suspended, removing from division";
                $log->save();
                $logIds[] = $log->id;
                // $this->line("{$user->fullname()} ($user->cid) Suspended.");
            }
        }
        // $this->info("Processed $i records. Now determining deletions. Complete log will be output after completion.");

        $pendingDeletions = User::where('cert_update', 0)->where('facility', '!=', 'ZZN');
        if ($pendingDeletions->count() + count($purgeIds) >= 500) {
            $this->log[] = "More than 500 records are going to be deleted... possible error. Aborting.";
            foreach ($logIds as $logId) {
                try {
                    Actions::find($logId)->delete();
                } catch (\Exception $e) {
                    // DB Error
                    continue;
                }
            }
        } else {
            if ($pendingDeletions->count() > 0) {
                $delUsers = $pendingDeletions->get();
                foreach ($delUsers as $delUser) {
                    if (in_array($delUser->cid, $errorIds)) {
                        continue;
                    }

                    switch ($delUser->rating) {
                        case -2:
                            $message = "Inactive";
                            break;
                        case -1:
                            $message = "Suspended";
                            break;
                        default:
                            $message = "Left division";
                            break;
                    }

                    $delUser->removeFromFacility("Automated", $message, "ZZN");
                    $delUser->flag_homecontroller = 0;
                    $delUser->cert_update = 1;
                    $delUser->save();
                    $this->log[] = "Deleted " . $delUser->fname . " " . $delUser->lname . " (" . $delUser->cid . ") (" . (($delUser->rating >= 0 ? Helper::ratingShortFromInt($delUser->rating) : "Suspended")) . ")";
                    $this->checkDeleted($delUser);
                    $deleted++;
                }
            }
        }
        if (count($purgeIds)) {
            foreach ($purgeIds as $purgeId) {
                User::find($purgeId)->purge();
                $deleted++;
            }
        }
        $this->log[] = "";
        $this->log[] = "Deleted: $deleted Active Members: " . User::where('facility',
                'NOT LIKE', "ZZN")->count();
        EmailHelper::sendEmail([
            "vatusa1@vatusa.net",
            "vatusa2@vatusa.net",
            // "vatusa6@vatusa.net",
        ], "CERT Sync", "emails.logsend", ['log' => $this->log]);
        // SMFHelper::createPost(7262, 83, "CERTSync Cycle", implode("\n", $this->log));

        foreach ($this->log as $line) {
            // $this->info($line);
        }
        // $this->line("Completed in " . (microtime(true) - $start) . " seconds");
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
