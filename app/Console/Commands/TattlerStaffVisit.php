<?php namespace App\Console\Commands;

use App\Classes\EmailHelper;
use App\Classes\SMFHelper;
use App\Facility;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\User;

class TattlerStaffVisit extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'TattlerStaffVisit';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks for staff who have been absent for at least 30 days.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
	    $report[] = "Staff Inactivity Report for " . date('M j, Y') . " only listing staff members whose activity was at least 30 days ago";
	    $report[] = "";
		$facs = Facility::where('active', 1)->orWhere('id', 'ZHQ')->get();
		foreach ($facs as $fac) {
		    if ($fac->id == "ZHQ") {

            } else {
                if ($fac->atm != "0") {
                    $webactivity = $fac->atm()->lastActivityWebsite();
                    $forumactivity = $fac->atm()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                "vatusa" . $fac->region . "@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " ATM is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->atm()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " ATM, " . $fac->atm()->fullname() . ", last active $activity days ago";
                    }
                }

                if ($fac->datm != "0") {
                    $webactivity = $fac->datm()->lastActivityWebsite();
                    $forumactivity = $fac->datm()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                $fac->id . "-atm@vatusa.net",
                                "vatusa" . $fac->region . "@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " DATM is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->datm()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " DATM, " . $fac->datm()->fullname() . ", last active $activity days ago";
                    }
                }

                if ($fac->ta != "0") {
                    $webactivity = $fac->ta()->lastActivityWebsite();
                    $forumactivity = $fac->ta()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                $fac->id . "-atm@vatusa.net",
                                "vatusa3@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " TA is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->ta()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " TA, " . $fac->ta()->fullname() . ", last active $activity days ago";
                    }
                }

                if ($fac->ec != "0") {
                    $webactivity = $fac->ec()->lastActivityWebsite();
                    $forumactivity = $fac->ec()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                $fac->id . "-atm@vatusa.net",
                                $fac->id . "-datm@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " EC is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->ec()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " EC, " . $fac->ec()->fullname() . ", last active $activity days ago";
                    }
                }

                if ($fac->fe != "0") {
                    $webactivity = $fac->fe()->lastActivityWebsite();
                    $forumactivity = $fac->fe()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                $fac->id . "-atm@vatusa.net",
                                $fac->id . "-datm@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " FE is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->fe()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " FE, " . $fac->fe()->fullname() . ", last active $activity days ago";
                    }
                }

                if ($fac->wm != "0") {
                    $webactivity = $fac->wm()->lastActivityWebsite();
                    $forumactivity = $fac->wm()->lastActivityForum();
                    // We want lowest
                    if ($webactivity > $forumactivity) $activity = $forumactivity;
                    else $activity = $webactivity;

                    if ($activity >= 30) {
                        EmailHelper::sendEmail(
                            [
                                $fac->id . "-atm@vatusa.net",
                                $fac->id . "-datm@vatusa.net",
                                "vatusa6@vatusa.net"
                            ],
                            "Tattler Staff Activity: " . $fac->id . " WM is likely inactive",
                            "emails.tattlers.staffactivity",
                            [
                                'name' => $fac->wm()->fullname(),
                                'days' => $activity
                            ]
                        );
                        $report[] = $fac->id . " WM, " . $fac->wm()->fullname() . ", last active $activity days ago";
                    }
                }
            }
        }

        $users = User::where('rating', \App\Classes\Helper::ratingIntFromShort("I1"))->where('facility','NOT LIKE','ZZN')->get();
		foreach($users as $user) {
		    $webactivity = $user->lastActivityWebsite();
		    $forumactivity = $user->lastActivityForum();
		    if ($webactivity > $forumactivity) $activity = $forumactivity;
		    else $activity = $webactivity;

            if ($activity >= 30) {
                EmailHelper::sendEmail(
                    [
                        $user->facility . "-ta@vatusa.net",
                        "vatusa3@vatusa.net",
                        "vatusa6@vatusa.net"
                    ],
                    "Tattler Staff Activity: " . $user->facility . " INS " . $user->fullname() . " is likely inactive",
                    "emails.tattlers.staffactivity",
                    [
                        'name' => $user->fullname(),
                        'days' => $activity
                    ]
                );
                $report[] = $user->facility . " INS " . $user->fullname() . ", last active $activity days ago";
            }
        }

        EmailHelper::sendEmail([
            "vatusa1@vatusa.net",
            "vatusa2@vatusa.net",
            "vatusa6@vatusa.net",
        ], "Tattler Staff Visit Report", "emails.logsend", ['log' => $report]);
        SMFHelper::createPost(7262, 82, "Tattler Staff Activity Report " . date('M j, Y'), implode("\n", $report));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
	    return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
        return [];
	}

}
