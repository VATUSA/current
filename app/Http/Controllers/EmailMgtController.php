<?php namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Facility;
use App\Classes\RoleHelper;
use App\Classes\EmailHelper;
use Auth;
use App\Classes\Helper;

class EmailMgtController extends Controller {
    public function getIndividual($cid) {
        if (!AuthHelper::authACL()->canSendBroadcastEmail()) {
            abort(401);
        }
        return view('mgt.mail.broadcast', ['cid' => $cid]);
    }

    public function getBroadcast($cid = null) {
        if (!AuthHelper::authACL()->canSendBroadcastEmail()) {
            abort(401);
        }
        return view('mgt.mail.broadcast', ['cid' => $cid]);
    }

    public function postBroadcast(Request $request) {
        if (!AuthHelper::authACL()->canSendBroadcastEmail()) {
            abort(401);
        }
        $rcpts = $request->recipients;
        $subj = $request->subject;
        $msg = $request->email;
        $single = $request->single;

        if ((empty($rcpts) && empty($single)) || empty($subj) || empty($msg)) {
            return back()->with('broadcastError', 'All fields are required.');
        }

        // Send to single person.
        if (empty($rcpts) && !empty($single)) {
            if (!EmailHelper::isOptedIn($single)) {
                return redirect("/mgt/mail/$single")
                    ->with('broadcastError', 'Could not send email. User is not opted in to broadcast emails.');
            }
            $email = Helper::emailFromCID($single);
            EmailHelper::sendEmailFrom($email, Auth::user()->email, Auth::user()->fname . " " . Auth::user()->lname, $subj, 'emails.mass', array('msg' => nl2br(strip_tags($msg, '<b><i>')), 'init' => Auth::user()->fname . " " . Auth::user()->lname . " (" . Auth::user()->cid . ")"));
            return redirect("/mgt/mail/$single")->with('broadcastSuccess', 'Email sent.');
        } else {
            // Handle Recipients
            // STAFF = All Staff
            // ALL = VATUSA Controllers
            // SRSTAFF = Facility Senior Staff
            // VATUSA = VATUSA Staff
            // INS = Instructional Staff (I1 + I3)
            // Z--/HCF[SJU/GUM] = Facility

            $emails = array();

            if ($rcpts == "STAFF") {
                $rcpts = "Facility Staff";
                foreach (Facility::get() as $f) {
                    if ($f->atm && EmailHelper::isOptedIn($f->atm)) {
                        $emails[] = $f->id . "-atm@vatusa.net";
                    }
                    if ($f->datm && EmailHelper::isOptedIn($f->datm)) {
                        $emails[] = $f->id . "-datm@vatusa.net";
                    }
                    if ($f->ta && EmailHelper::isOptedIn($f->ta)) {
                        $emails[] = $f->id . "-ta@vatusa.net";
                    }
                    if ($f->ec && EmailHelper::isOptedIn($f->ec)) {
                        $emails[] = $f->id . "-ec@vatusa.net";
                    }
                    if ($f->fe && EmailHelper::isOptedIn($f->fe)) {
                        $emails[] = $f->id . "-fe@vatusa.net";
                    }
                    if ($f->wm && EmailHelper::isOptedIn($f->wm)) {
                        $emails[] = $f->id . "-wm@vatusa.net";
                    }
                }
            } else if ($rcpts == "ALL") {
                $rcpts = "VATUSA";
                foreach (User::where('facility', 'NOT LIKE', 'ZZN')
                             ->where('facility', 'NOT LIKE', 'ZAE')
                             ->where('flag_broadcastOptedIn', true)->get() as $u) {
                    if ($u->email) {
                        $emails[] = $u->email;
                    }
                }
            } else if ($rcpts == "SRSTAFF") {
                $rcpts = "Facility Senior Staff";
                foreach (Facility::get() as $f) {
                    if ($f->atm && EmailHelper::isOptedIn($f->atm)) {
                        $emails[] = $f->id . "-atm@vatusa.net";
                    }
                    if ($f->datm && EmailHelper::isOptedIn($f->datm)) {
                        $emails[] = $f->id . "-datm@vatusa.net";
                    }
                    if ($f->ta && EmailHelper::isOptedIn($f->ta)) {
                        $emails[] = $f->id . "-ta@vatusa.net";
                    }
                }
            } else if ($rcpts == "DRCTR") {
                $rcpts = "Facility ATM/DATM";
                foreach (Facility::get() as $f) {
                    if (EmailHelper::isOptedIn($f->atm)) {
                        $emails[] = $f->id . "-atm@vatusa.net";
                    }
                    if (EmailHelper::isOptedIn($f->datm)) {
                        $emails[] = $f->id . "-datm@vatusa.net";
                    }
                }
            } else if ($rcpts == "WM") {
                $rcpts = "Facility Webmasters";
                foreach (Facility::get() as $f) {
                    if (EmailHelper::isOptedIn($f->wm)) {
                        $emails[] = $f->id . "-wm@vatusa.net";
                    }
                }
            } else if ($rcpts == "VATUSA") {
                $rcpts = "VATUSA Staff";
                foreach (Role::where('facility', 'ZHQ')->get() as $f) {
                    if (EmailHelper::isOptedIn($f->cid)) {
                        $emails[] = Helper::emailFromCID($f->cid);
                    }
                }
            } else if ($rcpts == "INS") {
                $rcpts = "Instructional staff";
                foreach (User::where('facility', 'NOT LIKE', 'ZZN')
                             ->where('facility', 'NOT LIKE', 'ZAE')
                             ->where('flag_broadcastOptedIn', true)->get() as $u) {
                    if (RoleHelper::isInstructor($u->cid, $u->facility, false) && $u->email) {
                        $emails[] = $u->email;
                    }
                }
            } else if ($rcpts == "ACE") {
                $rcpts = "ACE Team Members";
                foreach (Role::where('facility', 'ZHQ')->where('role', 'ACE')->get() as $f) {
                    if (EmailHelper::isOptedIn($f->cid)) {
                        $emails[] = Helper::emailFromCID($f->cid);
                    }
                }
            } else {
                foreach (User::where('facility', $rcpts)->where('flag_broadcastOptedIn', true)->get() as $u) {
                    $emails[] = $u->email;
                }
            }
            EmailHelper::sendEmailBCC(Auth::user()->email, Auth::user()->fname . " " . Auth::user()->lname, $emails, $subj, 'emails.mass', array('msg' => nl2br(strip_tags($msg, '<b><i>')), 'init' => Auth::user()->fname . " " . Auth::user()->lname . " (" . Auth::user()->cid . ")"));
        }
        $count = count($emails);
        if (!$count) {
            return redirect("/mgt/mail/broadcast")->with('broadcastError', 'No emails were sent.');
        }
        return redirect("/mgt/mail/broadcast")->with('broadcastSuccess', 'Email sent to ' . count($emails) . ' member' . ($count == 1 ? '' : 's'));
    }

    public function getWelcome() {
        if (!AuthHelper::authACL()->canManageFacilityTechConfig()) {
            abort(401);
        }

        $fac = Facility::find(Auth::user()->facility);
        return view('mgt.mail.welcome', ['welcome' => $fac->welcome_text]);
    }

    public function postWelcome(Request $request) {
        if (!AuthHelper::authACL()->canManageFacilityTechConfig()) {
            abort(401);
        }

        $fac = Facility::find(Auth::user()->facility);
        $fac->welcome_text = $request->input("welcome");
        $fac->save();
        return redirect("/mgt/mail/welcome")->with("success", "Welcome email set.");
    }
}
