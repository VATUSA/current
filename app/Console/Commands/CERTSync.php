<?php namespace App\Console\Commands;

define("CSV_CID", 0);
define("CSV_RATING", 1);
define("CSV_FNAME", 2);
define("CSV_LNAME", 3);
define("CSV_EMAIL", 4);

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Role;
use Illuminate\Console\Command;
use App\Classes\CertHelper;
use App\User;
use App\Transfers;
use App\Facility;
use App\Actions;

class CERTSync extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'CERTSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync our tables to CERT';

    public $log;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $added = 0;
        $rejoin = 0;
        $deleted = 0;
        $this->log = array();
        $data = CertHelper::downloadDivision();
        \DB::table("controllers")->update(["cert_update" => 0]);
        foreach ($data as $row2) {
            if (!$row2 || $row2 == "") continue;
            $row = str_getcsv($row2);
            if (!isset($row[1])) {
                echo "Error on row: $row2\n";
                continue;
            }

            if ($row[CSV_RATING] > 0 && $row[CSV_CID] > 800000) {
                $user = User::find($row[0]);
                if (!$user || $user == null) {
                    $user = new User();
                    $user->cid = $row[CSV_CID];
                    $user->fname = $row[CSV_FNAME];
                    $user->lname = $row[CSV_LNAME];
                    $user->email = $row[CSV_EMAIL];
                    $user->rating = $row[CSV_RATING];
                    $user->facility = "ZAE";
                    $user->facility_join = \DB::raw("NOW()");
                    //$user->created_at = \DB::raw("NOW()");
                    $user->flag_needbasic = 1;
                    $user->flag_xferOverride = 0;
                    $user->flag_homecontroller = 1;
                    $user->cert_update = 1;
                    $this->log[] = "Added " . $user->fname . " " . $user->lname . " (" . $user->cid . ") (" . Helper::ratingShortFromInt($user->rating) . ")";
                    echo "Adding " . $user->fname . " " . $user->lname . " (" . $user->cid . ").\n";
                    EmailHelper::sendEmail($user->email, "Welcome to VATUSA", "emails.user.join", []);

                    $log = new Actions();
                    $log->to = $user->cid;
                    $log->log = "Joined division, facility set to " . $user->facility . " by CERTSync";
                    $log->save();

                    $added++;
                } else {
                    $user->fname = $row[CSV_FNAME];
                    $user->lname = $row[CSV_LNAME];
                    $user->email = $row[CSV_EMAIL];
                    $user->rating = $row[CSV_RATING];
                    if ($user->flag_homecontroller == 0) {
                        // User is rejoining division... let's check and see if they left >6 months ago.
                        if (Transfers::where('cid', $row[CSV_CID])->where('actiontext', "Left division")
                            ->where(\DB::raw("DATE_ADD(created_at, INTERVAL 90 day)"), '>=', \DB::raw('NOW()'))
                            ->orderBy('created_at', 'DESC')->count()
                        ) {
                            $t = Transfers::where('cid', $row[CSV_CID])->where('actiontext', "Left division")
                                ->where(\DB::raw("DATE_ADD(created_at, INTERVAL 90 day)"), '>=', \DB::raw('NOW()'))
                                ->orderBy('created_at', 'DESC')->first();
                            $user->addToFacility($t->from);
                            $trans = new Transfers();
                            $trans->cid = $user->cid;
                            $trans->to = $t->from;
                            $trans->from = "ZZN";
                            $trans->status = 1;
                            $trans->actiontext = "Rejoined division";
                            $trans->reason = "Rejoined division";
                            $trans->save();
                            $log = new Actions();
                            $log->to = $user->cid;
                            $log->log = "Rejoined division, facility set to " . $user->facility . " by CERTSync";
                            $log->save();
                        } elseif (Transfers::where('cid', $row[CSV_CID])->where('actiontext', "Left division")
                            ->where(\DB::raw("DATE_ADD(created_at, INTERVAL 6 month)"), '<=', \DB::raw('NOW()'))
                            ->orderBy('created_at', 'DESC')->count()
                        ) {
                            $user->facility = "ZAE";
                            $user->facility_join = \DB::raw('NOW()');
                            $user->flag_needbasic = 1;
                            $trans = new Transfers();
                            $trans->cid = $user->cid;
                            $trans->to = $user->facility;
                            $trans->from = "ZZN";
                            $trans->status = 1;
                            $trans->actiontext = "Rejoined division";
                            $trans->reason = "Rejoined division";
                            $trans->save();

                            $log = new Actions();
                            $log->to = $user->cid;
                            $log->log = "Rejoined division, facility set to " . $user->facility . " by CERTSync";
                            $log->save();
                        } else {
                            $user->facility = "ZAE";
                            $user->facility_join = \DB::raw('NOW()');
                            $user->flag_needbasic = 0;
                            $trans = new Transfers();
                            $trans->cid = $user->cid;
                            $trans->to = $user->facility;
                            $trans->from = "ZZN";
                            $trans->status = 1;
                            $trans->actiontext = "Rejoined division";
                            $trans->reason = "Rejoined division";
                            $trans->save();

                            $log = new Actions();
                            $log->to = $user->cid;
                            $log->log = "Rejoined division, facility set to " . $user->facility . " by CERTSync";
                            $log->save();
                        }
                        // Now let us check to see if they have ever been in a facility.. if not, we need to override the need basic flag.
                        if (Transfers::where('cid', $row[CSV_CID])->where('to', 'NOT LIKE', 'ZAE')->where('to', 'NOT LIKE', 'ZZN')->count() < 1) {
                            $user->flag_needbasic = 1;
                            $user->save();
                        }

                        $this->log[] = "User rejoined division " . $user->fname . " " . $user->lname . " (" . $user->cid . ") (" . Helper::ratingShortFromInt($user->rating) . ")";
                        $rejoin++;
                    }
                    $user->flag_homecontroller = 1;
                    $user->cert_update = 1;
                }
                $user->save();
            } elseif ($row[CSV_RATING] == 0) {
                $user = User::find($row[CSV_CID]);
                if ($user != null) {
                    $user->cert_update = 0;
                    echo "Set " . $row[CSV_CID] . " cert_update to 0\n";
                }
            }
        }
        if (User::where('cert_update', 0)->where('facility', 'NOT LIKE', 'ZZN')->count() >= 500) {
            $this->log[] = "!!!!!!! More than 500 records weren't updated in this pass.  Not going to delete any of them";
        } elseif (User::where('cert_update', 0)->where('facility', 'NOT LIKE', 'ZZN')->count() > 0) {
            $users = User::where('cert_update', 0)->where('facility', 'NOT LIKE', 'ZZN')->get();
            foreach ($users as $user) {

                $user->removeFromFacility("Automated", "Left division", "ZZN");
                $user->flag_homecontroller = 0;
                $user->cert_update = 1;
                $user->save();
                $this->log[] = "Deleted " . $user->fname . " " . $user->lname . " (" . $user->cid . ") (" . Helper::ratingShortFromInt($user->rating) . ")";
                $this->checkDeleted($user);
                $deleted++;
            }
        }
        $this->log[] = "";
        $this->log[] = "Added: $added Rejoin: $rejoin Deleted: $deleted Active Members: " . User::where('facility', 'NOT LIKE', "ZZN")->count();
        EmailHelper::sendEmail([
            "vatusa1@vatusa.net",
            "vatusa2@vatusa.net",
           // "vatusa6@vatusa.net",
        ], "CERT Sync", "emails.logsend", ['log' => $this->log]);
        SMFHelper::createPost(7262, 83, "CERTSync Cycle", implode("\n", $this->log));
    }

    public function checkDeleted($user)
    {
        $removals = "";
        if ($user->facility == "ZAE" || $user->facility == "ZZN") return;
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

        if ($removals) {
            SMFHelper::createPost(7262, 82, "CERTSync: Staff deletion report for " . $user->fullname() . " (" . $user->cid . ")", $removals);
            $this->log[] = $removals;
        }
    }
}
