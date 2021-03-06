<?php namespace App\Http\Controllers;

use App\Classes\EmailHelper;
use App\Classes\ExamHelper;
use App\Classes\Helper;
use App\Classes\RoleHelper;
use App\Promotions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Transfers;
use Auth;
use App\Actions;
use App\User;
use App\Facility;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Wohali\OAuth2\Client\Provider\Discord;

class MyController
    extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getProfile(Request $request)
    {
        $checks = [];
        $eligible = Auth::user()->transferEligible($checks);

        /** Training Records */
        $trainingfac = $request->input('fac', null);
        $trainingfaclist = Auth::user()->trainingRecords()->groupBy('facility_id')->get();

        if (!$trainingfac) {
            if ($trainingfaclist->count() == 1) {
                $trainingfac = $trainingfaclist->first()->facility_id;
                $trainingfacname = Helper::facShtLng($trainingfac);
            } else {
                $trainingfac = Auth::user()->facility;
                $trainingfacname = Auth::user()->facility()->name;
            }
        } else {
            if (Facility::find($trainingfac)) {
                $trainingfacname = Helper::facShtLng($trainingfac);
            } else {
                abort(500);
            }
        }
        $trainingRecords = Auth::user()->trainingRecords()->with('instructor:cid,fname,lname')->where('facility_id',
            $trainingfac)->get();

        return view('my.profile',
            compact('checks', 'eligible', 'trainingRecords', 'trainingfac', 'trainingfacname', 'trainingfaclist'));
    }

    public function getAssignBasic()
    {
        if (Auth::user()->flag_needbasic) {
            if (!ExamHelper::isAssigned(Auth::user()->cid, BASIC_EXAM, true)) {
                ExamHelper::assign(Auth::user()->cid, BASIC_EXAM, 0, 14);

                return redirect('/exam/0')->with('success', "Basic exam assigned");
            } else {
                return redirect('/exam/0')->with('error', "The exam is already assigned or waiting for reassignment");
            }
        } else {
            return redirect('/my/profile')->with('error', "You are not eligible for the Basic ATC Exam");
        }
    }

    public function getSelect()
    {
        if (Auth::user()->selectionEligible()) {
            return View('my.facilityselect');
        }

        if (Auth::user()->facility()->active) {
            return redirect('/my/profile')->with('error', "You are already a member of a facility.");
        }

        return redirect('/info/join')->with('error', "You are not eligible to select a facility yet.");
    }

    public function postSelect(Request $request)
    {
        $facility = $request->facility;

        if (!Auth::user()->selectionEligible()) {
            return redirect('/info/join')->with("error", "You are not eligible to select a facility.");
        }

        if ($_POST['facility'] == "0") {
            return redirect('/my/transfer')->with('error', "You didn't select a facility!");
        }

        $facility = Facility::find($facility);
        if ($facility->active != 1) {
            return redirect('/my/select')->with("error", "Invalid facility selection");
        }

        Auth::user()->addToFacility($facility->id);
        $log = new Actions();
        $log->to = Auth::user()->cid;
        $log->log = "Initial facility selection " . $facility->id;
        $log->save();
        $transfer = new Transfers();
        $transfer->cid = Auth::user()->cid;
        $transfer->to = $facility->id;
        $transfer->from = "ZAE";
        $transfer->reason = "Initial selection";
        $transfer->status = 1;
        $transfer->actiontext = "Approved";
        $transfer->actionby = 0;
        $transfer->save();

        return redirect('/my/profile')->with('success', 'You have successfully joined ' . $facility->name);
    }

    public function getTransfer()
    {
        $user = User::where('cid', Auth::user()->cid)->first();
        if ($user->transferEligible()) {
            return view('my.transfer');
        }

        return redirect('/my/profile')->with('error', 'You are not currently eligible to transfer.');
    }

    public function doTransfer(Request $request)
    {
        $user = User::where('cid', Auth::user()->cid)->first();

        if (!$user->transferEligible()) {
            return redirect('/my/profile')->with('error', 'You are not currently eligible to transfer.');
        }

        if ($_POST['facility'] == "0") {
            return redirect('/my/transfer')->with('error', "You didn't select a facility!");
        }

        $fac = Facility::find($_POST['facility']);
        if (!$fac) {
            return redirect('/mgt/transfer')->with('error', "Invalid facility");
        }

        $this->validate($request, [
            'reason'   => 'required',
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
            $u = \App\User::where('cid', Auth::user()->cid)->first();
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
            "vatusa" . $fac->region . "@vatusa.net",
            "vatusa" . $user->facility()->region . "@vatusa.net"
        ], "Transfer Pending", "emails.transfers.internalpending", [
            'fname'    => $user->fname,
            'lname'    => $user->lname,
            'cid'      => $tr->cid,
            'facility' => $fac->id,
            'reason'   => $_POST['reason']
        ]);

        return redirect('/')->with('success', 'You have initiated a transfer to ' . $data->to);
    }

    public function getExamIndex()
    {
        return view('my.exams.index');
    }

    public function toggleBroadcastEmails(Request $request)
    {
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

    public function linkDiscord($mode = "link")
    {
        if ($mode === "link") {
            return Socialite::driver('discord')->setScopes(['identify'])->redirect();
        } elseif ($mode === "unlink") {
            $user = Auth::user();
            $user->discord_id = null;
            try {
                $user->saveOrFail();

                return response()->json(true);
            } catch (\Throwable $e) {
                return response()->json(false);
            }
        } elseif ($mode === "return") {
            try {
                $dUser = Socialite::driver('discord')->user();
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
}
