<?php namespace App\Http\Controllers;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\VATUSAMoodle;
use App\Helpers\DiscordHelper;
use App\Models\Actions;
use App\Models\Facility;
use App\Models\Transfers;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Wohali\OAuth2\Client\Provider\Discord;

class MyController
    extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }

    public function getProfile(Request $request) {
        $checks = [];
        $eligible = Auth::user()->transferEligible($checks);

        /** Training Records */
        $trainingfac = $request->input('fac', null);
        $trainingfaclist = Auth::user()->trainingRecords()->groupBy('facility_id')->get();

        if (!$trainingfac) {
            if ($trainingfaclist->count() == 1) {
                $facility = $trainingfaclist->first()->facility;
                $trainingfac = $facility->id;
                $trainingfacname = $facility->name;
            } else {
                $trainingfac = Auth::user()->facility;
                $trainingfacname = Auth::user()->facilityObj->name;
            }
        } else {
            if (Facility::find($trainingfac)) {
                $trainingfacname = Helper::facShtLng($trainingfac);
            } else {
                abort(500);
            }
        }

        $trainingFacListArray = array();
        foreach ($trainingfaclist as $tr) {
            $trainingFacListArray[$tr->facility_id] = $tr->facility->name;
        }
        if (!in_array(Auth::user()->facility, ["ZHQ", "ZAE", "ZZN"])) {
            $trainingFacListArray = array_merge($trainingFacListArray,
                [Auth::user()->facility => Auth::user()->facilityObj->name]);
        }
        $trainingRecords = Auth::user()->trainingRecords()->with('instructor:cid,fname,lname')->where('facility_id',
            $trainingfac)->get();

        $moodle = new VATUSAMoodle();
        try {
            $uid = $moodle->getUserId(Auth::user()->cid);
        } catch (\Exception $e) {
            $uid = -1;
        }
        $basicAssignmentDate = $moodle->getUserEnrolmentTimestamp($uid,
            config('exams.BASIC.enrolId'));
        $s2AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid,
            config('exams.S2.enrolId'));
        $s3AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid,
            config('exams.S3.enrolId'));
        $c1AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid,
            config('exams.C1.enrolId'));

        $examAttempts = [
            'Basic ATC/S1 Exam' => [
                'examInfo' => config('exams.BASIC'),
                'assignDate' => $basicAssignmentDate ?
                    Carbon::createFromTimestampUTC($basicAssignmentDate)->format('Y-m-d H:i') : false,
                'attempts' => $moodle->getQuizAttempts(config('exams.BASIC.id'), null, $uid)],
            'S2 Rating (TWR) Controller Exam' => [
                'examInfo' => config('exams.S2'),
                'assignDate' => $s2AssignmentDate ?
                    Carbon::createFromTimestampUTC($s2AssignmentDate)->format('Y-m-d H:i') : false,
                'attempts' => $moodle->getQuizAttempts(config('exams.S2.id'), null, $uid)],
            'S3 Rating (DEP/APP) Controller Exam' => [
                'examInfo' => config('exams.S3'),
                'assignDate' => $s3AssignmentDate ?
                    Carbon::createFromTimestampUTC($s3AssignmentDate)->format('Y-m-d H:i') : false,
                'attempts' => $moodle->getQuizAttempts(config('exams.S3.id'), null, $uid)],
            'C1 Rating (CTR) Controller Exam' => [
                'examInfo' => config('exams.C1'),
                'assignDate' => $c1AssignmentDate ?
                    Carbon::createFromTimestampUTC($c1AssignmentDate)->format('Y-m-d H:i') : false,
                'attempts' => $moodle->getQuizAttempts(config('exams.C1.id'), null, $uid)],
        ];

        return view('my.profile',
            compact('checks', 'eligible', 'trainingRecords', 'trainingfac', 'trainingfacname', 'trainingfaclist', 'trainingFacListArray', 'examAttempts'));
    }

    public function getTransfer() {
        $user = User::where('cid', Auth::user()->cid)->first();
        if ($user->transferEligible()) {
            return view('my.transfer');
        }

        return redirect('/my/profile')->with('error', 'You are not currently eligible to transfer.');
    }

    public function doTransfer(Request $request) {
        $user = User::where('cid', Auth::user()->cid)->first();

        if (!$user->transferEligible()) {
            return redirect('/my/profile')->with('error', 'You are not currently eligible to transfer.');
        }

        if ($_POST['facility'] == "0") {
            return redirect('/my/transfer')->with('error', "You didn't select a facility!");
        }

        $fac = Facility::find($_POST['facility']);
        if (!$fac) {
            return redirect('/my/transfer')->with('error', "Invalid facility");
        }

        if(Auth::user()->facility == $_POST['facility']){
            return redirect('/my/transfer')->with('error', "You cannot transfer to your current facility");
        }

        $this->validate($request, [
            'reason' => 'required|max:2048',
            'facility' => 'required|max:3|min:3',
        ]);

        $tr = new Transfers;
        $tr->cid = Auth::user()->cid;
        $tr->to = $_POST['facility'];
        $tr->from = Auth::user()->facility;
        $tr->reason = $_POST['reason'];
        $tr->save();
        $id = $tr->id;
        $data = Transfers::where('id', $id)->first();
        $log = new Actions;
        $log->from = 0;
        $log->to = Auth::user()->cid;
        $log->log = "Requested transfer from " . $data->from . " to " . $data->to . ": " . $data->reason;
        $log->save();

        if (Auth::user()->flag_xferOverride) {
            $u = \App\Models\User::where('cid', Auth::user()->cid)->first();
            $u->flag_xferOverride = 0;
            $u->save();
            $log = new Actions;
            $log->from = 0;
            $log->to = Auth::user()->cid;
            $log->log = "Transfer override flag removed";
            $log->save();
        }

        EmailHelper::sendEmail([
            $tr->from . "-atm@vatusa.net",
            $tr->from . "-datm@vatusa.net",
            $tr->to . "-atm@vatusa.net",
            $tr->to . "-datm@vatusa.net",
            "vatusa2@vatusa.net",
        ], "Transfer Pending", "emails.transfers.internalpending", [
            'fname' => $user->fname,
            'lname' => $user->lname,
            'cid' => $tr->cid,
            'facility' => $fac->id,
            'reason' => $_POST['reason']
        ]);

        return redirect('/')->with('success', 'You have initiated a transfer to ' . $data->to);
    }

    public function toggleBroadcastEmails(Request $request) {
        if (!$request->ajax()) {
            abort(500);
        }


        $user = Auth::user();
        $currentFlag = $user->flag_broadcastOptedIn;
        $user->flag_broadcastOptedIn = !$currentFlag;
        $user->saveOrFail();

        $log = new Actions();
        $log->from = 0;
        $log->to = Auth::id();
        $log->log = "Opted " . ($currentFlag ? "out of" : "in to") . " broadcast emails";
        $log->save();

        return "1";
    }

    public function toggleNamePrivacy(Request $request) {
        if (!$request->ajax()) {
            abort(500);
        }


        $user = Auth::user();
        $currentFlag = $user->flag_nameprivacy;
        $user->flag_nameprivacy = !$currentFlag;
        $user->saveOrFail();

        $log = new Actions();
        $log->from = 0;
        $log->to = Auth::id();
        $log->log = "Opted " . ($currentFlag ? "out of" : "in to") . " name privacy";
        $log->save();

        return "1";
    }

    public function linkDiscord($mode = "link") {
        if ($mode === "link") {
            return Socialite::driver('discord')->setScopes(['identify'])->redirect();
        } else if ($mode === "unlink") {
            $user = Auth::user();
            $user->discord_id = null;
            try {
                $user->saveOrFail();

                return response()->json(true);
            } catch (\Throwable $e) {
                return response()->json(false);
            }
        } else if ($mode === "return") {
            try {
                $dUser = Socialite::driver('discord')->stateless()->user();
                $user = Auth::user();
                $user->discord_id = $dUser->getId();
                try {
                    $user->saveOrFail();

                    return redirect()->to('/my/profile')->with('discordError', false);
                } catch (\Throwable $e) {
                    return redirect()->to('/my/profile')->with('discordError', true);
                }

            } catch (Exception $e) {
                return redirect()->to('/my/profile')->with('discordError', true);
            }
        }

        abort(400, "Invalid Mode");
    }

    public function assignRoles() {
        $user = Auth::user();
        DiscordHelper::assignRoles($user->cid);
        return "1";
    }
}
