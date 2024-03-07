<?php namespace App\Http\Controllers;

use App\Helpers\RoleHelperV2;
use App\Models\Actions;
use App\Models\ChecklistData;
use App\Models\Checklists;
use App\Classes\Helper;
use App\Classes\PromoHelper;
use App\Classes\SMFHelper;
use App\Classes\VATUSAMoodle;
use App\Models\OTSEval;
use App\Models\OTSEvalForm;
use App\Models\Role;
use App\Models\SoloCert;
use App\Models\TrainingRecord;
use App\Models\Transfers;
use Carbon\Carbon;
use Exception;
use Faker\Factory;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Facility;
use App\Classes\RoleHelper;
use App\Classes\EmailHelper;
use Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class MgtController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('ins');
    }

    public function getController(Request $request, $cid = null)
    {
        if (!RoleHelper::isMentor() && !RoleHelper::isInstructor() && !RoleHelper::isFacilitySeniorStaff()
            && !RoleHelper::isVATUSAStaff() && !RoleHelper::isWebTeam()
            && !RoleHelper::hasRole(Auth::user()->cid, Auth::user()->facility, "WM")) {
            abort(401);
        }

        if ($cid == null) {
            return view('mgt.controller.blank');
        }

        if ($cid == "Katniss") {
            return view('eastereggs.katniss');
        }

        $user = User::where('cid', $cid);

        if ($user->count()) {
            $user = $user->first();
            $checks = [];
            $eligible = $user->transferEligible($checks);
            $user->promotionEligible();

            /** Training Records */
            $trainingfac = $request->input('fac', null);
            $trainingfaclist = $user->trainingRecords()->groupBy('facility_id')->get()->filter(function ($record) use (
                $user
            ) {
                return RoleHelper::isTrainingStaff(null, true, $record->facility_id)
                    || RoleHelper::isTrainingStaff(null, true, $user->facility)
                    || RoleHelper::isWebTeam();
            });

            if (!$trainingfac) {
                if ($trainingfaclist->count() == 1) {
                    $trainingfac = $trainingfaclist->first()->facility_id;
                    $trainingfacname = Helper::facShtLng($trainingfac);
                } else {
                    $trainingfac = $user->facilityObj->id;
                    $trainingfacname = $user->facilityObj->name;
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
            if (!in_array($user->facility, ["ZHQ", "ZAE", "ZZN"])) {
                $trainingFacListArray = array_merge($trainingFacListArray,
                    [$user->facility => $user->facilityObj->name]);
            }
            foreach ($user->visits()->get() as $visit) {
                $trainingFacListArray[$visit->fac->id] = $visit->fac->name;
            }
            $trainingRecords = RoleHelper::isTrainingStaff(null, true, $trainingfac)
            || RoleHelper::isTrainingStaff(null, true, $user->facility)
            || RoleHelper::isFacilitySeniorStaff()
            || RoleHelper::isWebTeam()
                ? $user->trainingRecords()->where('facility_id', $trainingfac)->get() : [];
            $canAddTR = RoleHelper::isTrainingStaff(Auth::user()->cid, true, $trainingfac)
                && $user->cid !== Auth::user()->cid;

            //Get INS at ARTCC
            $ins = ['ins' => [], 'mtr' => []];
            $users = User::where('facility', $trainingfac)
                ->where('rating', '>=', Helper::ratingIntFromShort("I1"))
                ->where('rating', '<=', Helper::ratingIntFromShort("I3"))
                ->get();
            if ($users) {
                foreach ($users as $tUser) {
                    $ins['ins'][$tUser->cid] = $tUser->fullname();
                }
            }
            $users = Role::where('facility', $trainingfac)->where('role', 'INS')->get();
            if ($users) {
                foreach ($users as $tUser) {
                    $ins['ins'][$tUser->cid] = Helper::nameFromCID($tUser->cid);
                }
            }
            $users = Role::where('facility', $trainingfac)->where('role', 'MTR')->get();
            if ($users) {
                foreach ($users as $tUser) {
                    $ins['mtr'][$tUser->cid] = Helper::nameFromCID($tUser->cid);
                }
            }
            foreach ($ins as $type => $users) {
                asort($ins[$type]);
            }

            $moodle = new VATUSAMoodle();
            try {
                $uid = $moodle->getUserId($cid);
            } catch (Exception $e) {
                $uid = -1;
            }
            $moodleUid = $uid;
            $basicAssignmentDate = $moodle->getUserEnrolmentTimestamp($uid, config('exams.BASIC.enrolId'));
            $s2AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid, config('exams.S2.enrolId'));
            $s3AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid, config('exams.S3.enrolId'));
            $c1AssignmentDate = $moodle->getUserEnrolmentTimestamp($uid, config('exams.C1.enrolId'));

            $examAttempts = [
                'Basic ATC/S1 Exam' => array_merge([
                    'examInfo' => config('exams.BASIC'),
                    'assignDate' => $basicAssignmentDate ?
                        Carbon::createFromTimestampUTC($basicAssignmentDate)->format('Y-m-d H:i') : false
                ], ['attempts' => $moodle->getQuizAttempts(config('exams.BASIC.id'), null, $uid)]),
                'S2 Rating (TWR) Controller Exam' => array_merge([
                    'examInfo' => config('exams.S2'),
                    'assignDate' => $s2AssignmentDate ?
                        Carbon::createFromTimestampUTC($s2AssignmentDate)->format('Y-m-d H:i') : false
                ], ['attempts' => $moodle->getQuizAttempts(config('exams.S2.id'), null, $uid)]),
                'S3 Rating (DEP/APP) Controller Exam' => array_merge([
                    'examInfo' => config('exams.S3'),
                    'assignDate' => $s3AssignmentDate ?
                        Carbon::createFromTimestampUTC($s3AssignmentDate)->format('Y-m-d H:i') : false
                ],
                    ['attempts' => $moodle->getQuizAttempts(config('exams.S3.id'), null, $uid)]),
                'C1 Rating (CTR) Controller Exam' => array_merge([
                    'examInfo' => config('exams.C1'),
                    'assignDate' => $c1AssignmentDate ?
                        Carbon::createFromTimestampUTC($c1AssignmentDate)->format('Y-m-d H:i') : false
                ],
                    ['attempts' => $moodle->getQuizAttempts(config('exams.C1.id'), null, $uid)]),
            ];

            $assignedRoles = RoleHelperV2::assignedRoles($cid);

            return view('mgt.controller.index',
                compact('user', 'checks', 'eligible', 'trainingRecords', 'trainingFacListArray', 'trainingfac',
                    'trainingfacname', 'ins', 'canAddTR', 'examAttempts', 'moodleUid', 'assignedRoles'));
        } else {
            if ($user = User::where('discord_id', $cid)->first()) {
                return redirect()->route('mgt.controller.index', ['cid' => $user->cid]);
            }

            return view('mgt.controller.404');
        }
    }

    /* Controller AJAX */
    public function getControllerTransfers(Request $request, $cid)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isInstructor() && !RoleHelper::isFacilityStaff() && !RoleHelper::isVATUSAStaff()
            && !RoleHelper::isWebTeam()) {
            abort(401);
        }

        $transfers = Transfers::where('cid', $cid)->where('status', '<', 2)->orderBy('updated_at', 'ASC')->get();
        $data = [];
        foreach ($transfers as $transfer) {
            $temp = [
                'id' => $transfer->id,
                'date' => substr($transfer->updated_at, 0, 10),
                'from' => $transfer->from,
                'to' => $transfer->to
            ];
            $data[] = $temp;
        }
    }

    public function postControllerRating(Request $request, $cid)
    {
        if (!$request->ajax()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(500);
        }

        $user = User::find($cid);
        if (!$user) {
            abort(404);
        }
        if ($user->rating < Helper::ratingIntFromShort("OBS") || $user->rating > Helper::ratingIntFromShort("I3")) {
            abort(401);
        }

        if (!is_numeric($request->input('rating'))) {
            abort(500);
        }

        $from = $user->rating;
        $rating = $request->input('rating');

        if ($rating > Helper::ratingIntFromShort("I3")) {
            abort(400);
        }

        if (env('APP_ENV', 'dev') == "prod") {
            $return = PromoHelper::handle($cid, Auth::user()->cid, $rating, [
                "exam" => "0000-00-00 00:00:00",
                "examiner" => Auth::user()->cid,
                "position" => "n/a"
            ]);

            if ($return) {
                echo "1";
            } else {
                echo "0";
            }
        }

        echo "1";

        return;
    }

    public function getControllerTransferWaiver(Request $request, $cid)
    {
        if (!$request->ajax()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(500);
        }

        $user = User::where('cid', $cid)->first();
        if ($user == null) {
            abort(404);
        }

        if ($user->flag_xferOverride) {
            $user->flag_xferOverride = 0;
        } else {
            $user->flag_xferOverride = 1;
        }

        $user->save();

        $action = new Actions();
        $action->to = $user->cid;
        $action->log = "Transfer Waiver " . (($user->flag_xferOverride == 1) ? "enabled" : "disabled") . " by " .
            Auth::user()->fullname() . " " . Auth::user()->cid;
        //$action->created_at = \DB::raw("NOW()");
        $action->save();

        echo $user->flag_xferOverride;

        return;
    }

    public function getControllerToggleBasic($cid)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $user = User::find($cid);
        if (!$user) {
            return redirect("/mgt/controller")->with("error", "Cannot find user with that CID");
        }

        $user->toggleBasic();

        return redirect("/mgt/controller/$cid")->with("success", "Basic Exam Requirement Toggled");
    }

    /*
     * Ace Team
     */
    public function getAce()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $roles = Role::where('role', 'ACE')->orderBy('cid')->get();

        return view('mgt.ace', ['roles' => $roles]);
    }

    /*
     * Division Staff Management
     */
    public function getStaff()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        return view('mgt.staff');
    }

    public function deleteStaff(Request $request, $role)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $roles = Role::where('role', $role)->where('facility', 'ZHQ')->get();
        foreach ($roles as $r) {
            $cid = $r->cid;
            $log = new Actions();
            $log->to = $cid;
            $log->log = "Removed from role '" . RoleHelper::roleTitle($r->role) . "' by " . Auth::user()->fullname();
            $log->save();

            //Delete Email
            /*EmailHelper::setForward('vat' . str_replace('US', 'usa', $r->role) . '@vatusa.net',
                'vatusa2@vatusa.net');

            foreach($previous as $email) {
                EmailHelper::deleteEmail($email);
            }
            */
            $user = User::find($cid);
            if ($user->facility == config('staff.hq.HQ')) {
                $tr = new \App\Models\Transfers();
                $tr->cid = $cid;
                $tr->reason = "Auto Transfer to ZAE: removed from staff.";
                $tr->to = "ZAE";
                $tr->from = config('staff.hq.HQ');
                $tr->status = 1;
                $tr->actionby = 0;
                $tr->save();

                $user->flag_xferOverride = 1;
                $user->save();
            }

            $r->delete();
            SMFHelper::setPermissions($cid);
        }
    }

    public function putStaff(Request $request, $role)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $cid = $request->cid;
        $xfer = $request->xfer;

        $this->deleteStaff($request, $role);

        $nrole = new Role();
        $nrole->cid = $cid;
        $nrole->role = $role;
        $nrole->facility = "ZHQ";
        $nrole->created_at = \Carbon\Carbon::now();
        $nrole->save();

        //Add Email
        /*
        $previous = EmailHelper::forwardDestination('vat' . str_replace('us', 'usa', $role).'@vatusa.net');

        foreach($previous as $email) {
            EmailHelper::deleteEmail($email);
        }
        $user = User::find($cid);
        $email = strtolower(substr($user->fname, 0, 1) . "." . $user->lname) . "@vatusa.net";
        EmailHelper::addEmail($email, env('APP_KEY'));
        EmailHelper::setForward('vat' . str_replace('US', 'usa', $role) . '@vatusa.net', $email);
        */

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Assigned to role '" . RoleHelper::roleTitle($role) . "' by " . Auth::user()->fullname();
        $log->save();

        //ONLY TRANSFER IF NOT IN ZHQ AND FLAG ENABLED
        $u = User::find($cid);
        if ($u->facility != config('staff.hq.HQ') && $xfer == "true") {
            $tr = new \App\Models\Transfers();

            $tr->cid = $cid;
            $tr->reason = "Auto Transfer to " . config('staff.hq.HQ') . ": set as staff.";
            $tr->to = config('staff.hq.HQ');
            $tr->from = $u->facility;
            $tr->status = 1;
            $tr->actionby = 0;
            $tr->save();

            $log = new Actions();
            $log->to = $u->cid;
            $log->from = 0;
            $log->log = "Auto Transfer to " . $tr->to . ": set as staff.";
            $log->save();

            EmailHelper::sendEmail(
                [
                    "{$u->facility}-atm@vatusa.net",
                    "{$u->facility}-datm@vatusa.net",
                    "vatusa2@vatusa.net"
                ],
                "Removal from {$u->facilityObj->name}",
                "emails.user.removed",
                [
                    'name' => $u->fname . " " . $u->lname,
                    'facility' => $u->facilityObj->name,
                    'by' => "Automated",
                    'msg' => "Auto Transfer to " . $tr->to . ": set as staff.",
                    'facid' => $u->facility,
                    'region' => $u->facilityObj->region,
                    'obsInactive' => 0
                ]
            );

            $u->addToFacility($tr->to);
        }
        SMFHelper::setPermissions($cid);
    }

    public function addLog(Request $request)
    {
        $this->validate($request, [
            'to' => 'required',
            'log' => 'required|min:1',
        ]);

        $user = User::find($request->to);

        if (!$user) {
            abort(404);
        }

        if (!RoleHelper::isFacilitySeniorStaff()) {
            abort(401);
        }

        $le = new Actions;
        $le->to = $request->to;
        $le->from = Auth::user()->cid;
        $le->log = $request->log;
        $le->save();

        EmailHelper::sendEmail(
            [
                Auth::user()->email,
                "vatusa1@vatusa.net",
                "vatusa2@vatusa.net"
            ],
            "{$user->fullname()} - {$user->cid} Action Log Update",
            "emails.user.actionLog",
            [
                'user' => $user,
                'by' => Auth::user(),
                'msg' => $request->log
            ]
        );

        return redirect('/mgt/controller/' . $request->to . '#actions')->with('success',
            'Your log entry has been added.');
    }

    public function getManualTransfer(Request $request)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        return view('mgt.transfer', ['cid' => $request->input("cid", '')]);
    }

    public function postManualTransfer(Request $request)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $cid = $request->input("cid");
        $reason = $request->input("reason");
        $facility = $request->input("facility");

        if (!$cid || !$reason || !$facility) {
            return redirect("/mgt/transfer")->with("error", "All items are required");
        }

        $user = User::find($cid);
        if (!$user) {
            return redirect("/mgt/transfer")->with("error", "User not found");
        }
        if ($user->flag_homecontroller != 1) {
            return redirect("/mgt/controller/{$cid}")->with("error", "User is not a member of the VATUSA Division");
        }

        if (Transfers::where('cid', $cid)->where('status', 0)->count() > 0) {
            return redirect("/mgt/transfer")->with("error", "User has pending transfer request.");
        }

        $from = $user->facility;

        $tr = new Transfers;
        $tr->cid = $cid;
        $tr->to = $facility;
        $tr->from = $user->facility;
        $tr->reason = $reason;
        $tr->save();
        $fac = Facility::find($facility);
        $log = new Actions;
        $log->from = 0;
        $log->to = $cid;
        $log->log =
            "[Submitted by " . Auth::user()->fullname() . "] Requested transfer from " . $tr->from . " to " . $tr->to .
            ": " . $tr->reason;
        $log->save();
        if (in_array($facility, ['ZAE', 'ZHQ'])) {
            // Automatically approve transfers to ZAE, ZHQ
            $tr->accept(Auth::user()->cid);
        } else {
            EmailHelper::sendEmail([
                $tr->to . "-atm@vatusa.net",
                $tr->to . "-datm@vatusa.net",
                "vatusa2@vatusa.net"
            ], "Transfer Pending", "emails.transfers.internalpending", [
                'fname' => $user->fname,
                'lname' => $user->lname,
                'cid' => $tr->cid,
                'facility' => $fac->id,
                'reason' => $_POST['reason']
            ]);
        }
        return redirect("/mgt/transfer")->with("success", "Transfer for $cid - " . $user->fullname() . " submitted.");
    }

    function getSolo()
    {
        if (!RoleHelper::isFacilitySeniorStaff() && !RoleHelper::isInstructor() && !RoleHelper::isVATUSAStaff()
            && !RoleHelper::isWebTeam()) {
            abort(401);
        }

        return view('mgt.solo');
    }

    function postSolo(Request $request)
    {
        if (!RoleHelper::isFacilitySeniorStaff() && !RoleHelper::isInstructor() && !RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $user = User::find($request->input('cid'));
        if (!$user) {
            return redirect('/mgt/solo')->with('error', "Invalid CID");
        }
        if (!RoleHelper::isInstructor(Auth::user()->cid,
                $user->facility) && !RoleHelper::isFacilitySeniorStaff(Auth::user()->cid,
                $user->facility) && !RoleHelper::isVATUSAStaff()) {
            return redirect('/mgt/solo')->with('error',
                'You do not have permission to assign this solo certification.');
        }

        if (!preg_match("/^([A-Z0-9]{2,3})_(TWR|APP|CTR)$/i", $request->input("position"))) {
            return redirect("/mgt/solo")->with("error", "Invalid position defined.");
        }

        $exp = $request->input("expiration", null);
        if (!$exp || !preg_match("/^\d{4}-\d{2}-\d{2}/", $exp)) {
            return redirect("/mgt/solo")->with("error",
                "Expiration date is malformed. Try a different browser.");
        }
        if (Carbon::createFromFormat('Y-m-d', $exp)->diffInDays() > 30) {
            return redirect("/mgt/solo")->with("error", "Expiration date cannot be more than 30 days away.");
        }

        $solo = new SoloCert();
        $solo->cid = $request->input('cid');
        $solo->position = $request->input('position');
        $solo->expires = $request->input('expiration');
        $solo->save();

        return redirect('/mgt/solo')->with('success', 'Added solo certification');
    }

    function deleteSolo(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(500);
        }

        $cert = SoloCert::find($id);
        if (!$cert) {
            abort(404);
        }

        $user = User::find($cert->cid);
        if (!$user) {
            abort(500);
        }
        if (!RoleHelper::isInstructor(Auth::user()->cid,
                $user->facility) && !RoleHelper::isFacilitySeniorStaff(Auth::user()->cid,
                $user->facility) && !RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $cert->delete();

        return session()->flash('success', 'Removed solo certification');
    }

    function getControllerPromote($cid)
    {
        $user = User::find($cid);
        $forms = OTSEvalForm::active()->get();

        if (!$user) {
            return redirect('mgt/facility#mem')->with('error', 'User not found.');
        }

        if (!RoleHelper::isFacilitySeniorStaff(Auth::user()->cid,
                $user->facility) && !RoleHelper::isInstructor(Auth::user()->cid,
                $user->facility) && !RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        if (!$forms) {
            return redirect('mgt/facility#mem')->with('error',
                'No evaluation forms found. Please try again later or contact VATUSA6.');
        }
        if ($forms->count() !== 4) {
            return redirect('mgt/facility#mem')->with('error',
                'Insufficient evaluation forms found. Please try again later or contact VATUSA6.');
        }

        if (!$user->promotionEligible()) {
            return redirect('/mgt/facility#mem')->with('error', 'User is not eligible');
        }

        $user->checkPromotionCriteria($trainingRecordStatus, $otsEvalStatus, $examPosition, $dateOfExam, $evalId);

        return view('mgt.controller.promotion',
            compact('user', 'forms', 'trainingRecordStatus',
                'otsEvalStatus', 'examPosition', 'dateOfExam', 'evalId'));
    }

    function postControllerPromote(Request $request, $cid)
    {
        $user = User::find($cid);
        if (!$user) {
            return redirect('mgt/facility#mem')->with('error', 'User not found');
        }

        if (!RoleHelper::isFacilitySeniorStaff(Auth::user()->cid,
                $user->facility) && !RoleHelper::isInstructor(Auth::user()->cid,
                $user->facility) && !RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        if (!$user->promotionEligible()) {
            return redirect('/mgt/facility#mem')->with('error', 'User is not eligible');
        }

        $examiner = $request->input('examiner');
        $exam = $request->input('year') . "-" . $request->input('month') . "-" . $request->input('day');
        $position = $request->input('position');
        if ($examiner == "" || $exam == "--" || $position == "") {
            return redirect('mgt/controller/' . $cid . '/promote')->with('error', 'All fields required.');
        }

        $return = PromoHelper::handle($cid, Auth::user()->cid, $user->rating + 1,
            ['exam' => $exam, 'examiner' => $examiner, 'position' => $position]);
        if ($return) {
            return redirect('mgt/facility#mem')->with('success', 'User successfully promoted');
        }
    }

// Checklists
    public function getChecklists()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $checklists = Checklists::orderBy('order', 'ASC')->get();

        return view('mgt.checklists.checklists', ['checklists' => $checklists]);
    }

    public function getChecklistItems($id)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $checklist = Checklists::find($id);
        if (!$checklist) {
            abort(404);
        }

        return view('mgt.checklists.checklist', ['cl' => $checklist]);
    }

    public function postChecklistsOrder()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $x = 1;

        foreach ($_POST['cl'] as $list) {
            $blockModel = Checklists::find($list);
            $blockModel->order = $x;
            $blockModel->save();
            $x++;
        }

        echo 1;
    }

    public function postChecklistItemsOrder()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $x = 1;

        foreach ($_POST['cl'] as $list) {
            $cli = ChecklistData::find($list);
            $cli->order = $x;
            $cli->save();
            $x++;
        }

        echo 1;
    }

    public function putChecklists()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $list = new Checklists();
        $list->name = "New Training Checklist";
        $list->active = 1;

        $highCh = Checklists::orderBy('order', 'DESC')->first();
        if ($highCh) {
            $order = $highCh->order + 1;
        } else {
            $order = 1;
        }
        $list->order = $order;

        $list->save();

        echo $list->id;
    }

    public function postChecklist($id)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $cl = Checklists::find($id);
        if (!$cl) {
            abort(404);
        }

        if ($_POST['name']) {
            $cl->name = $_POST['name'];
        }
        $cl->save();

        echo "1";
    }

    public function deleteChecklist($clid)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $cl = Checklists::where('id', $clid)->first();

        foreach ($cl->items as $it) {
            $it->delete();
        }

        $cl->delete();

        $x = 1;

        foreach (Checklists::orderBy('order', 'ASC')->get() as $it) {
            $it->order = $x;
            $it->save();
            $x++;
        }
    }

    public function putChecklistItem($id)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        parse_str(file_get_contents("php://input"), $vars);
        $list = new ChecklistData();
        $list->item = $vars['name'];
        $list->checklist_id = $id;

        $highCh = ChecklistData::where('checklist_id', $id)->orderBy('order', 'DESC')->first();
        if ($highCh) {
            $order = $highCh->order + 1;
        } else {
            $order = 1;
        }
        $list->order = $order;

        $list->save();

        echo $list->id;
    }

    public function postChecklistItem($clid, $id)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $cl = ChecklistData::find($id);
        if (!$cl) {
            abort(404);
        }

        if ($_POST['name']) {
            $cl->item = $_POST['name'];
        }
        $cl->save();

        echo "1";
    }

    public function deleteChecklistItem($clid, $id)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $item = ChecklistData::where('id', $id)->first();

        $item->delete();

        $x = 1;

        foreach (ChecklistData::where('checklist_id', $clid)->orderBy('order', 'ASC')->get() as $it) {
            $it->order = $x;
            $it->save();
            $x++;
        }
    }

    /**
     * Delete user's log entry
     *
     * @param int $log
     *
     * @return string
     */
    public function deleteActionLog($log)
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $log = Actions::findOrFail($log);

        if (!$log->from || str_contains($log->log,
                'by ' . Helper::nameFromCID($log->from))) {
            //By System, not deletable
            abort(422);
        }

        $log->delete();

        return "1";
    }

    public function toggleStaffPrevent(Request $request)
    {
        $cid = $request->cid;

        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $user = User::findOrFail($cid);
        $currentFlag = $user->flag_preventStaffAssign;
        $user->flag_preventStaffAssign = !$currentFlag;
        $user->save();

        return "1";
    }

    public function ajaxCanModifyRecord($record)
    {
        $record = TrainingRecord::find($record);
        if (!$record->count()) {
            return response()->json(false);
        }

        return response()->json(Auth::check() && $record->student_id != Auth::user()->cid &&
            (RoleHelper::isVATUSAStaff() || !in_array($record->ots_status, [1, 2])) &&
            (RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, $record->facility) ||
                (RoleHelper::isTrainingStaff(Auth::user()->cid, true, $record->facility)
                    && $record->instructor_id == Auth::user()->cid)));
    }

    public function getOTSEval(Request $request, int $cid, $form = null)
    {
        $student = User::find($cid);
        if (!$student) {
            abort(404);
        }
        if (!RoleHelper::isInstructor(Auth::user()->cid, $student->facility)) {
            abort(403);
        }
        $form = $form ? OTSEvalForm::has('perfcats')
            ->has('perfcats.indicators')->withAll()->find($form)
            : OTSEvalForm::has('perfcats')->has('perfcats.indicators')
                ->withAll()->where('rating_id', $student->rating + 1)->first();
        if (!$student || !$form) {
            abort(404, "The OTS evaluation form is invalid.");
        }
        if ($form->rating_id !== $student->rating + 1 || !$student->promotionEligible()) {
            return redirect('/mgt/facility#mem')->with('error', 'The controller is not eligible for that evaluation.');
        }

        if ($request->has('withRedirect') && $request->has('url') && $returnFac = $student->facilityObj) {
            if ($returnUrl = $returnFac->returnPaths()->whereOrder($request->url)->first()) {
                return redirect()->to($request->url())->with('evalRedirect', $returnUrl->url);
            }
        }

        return response()->view('mgt.controller.training.otsEval', compact('student', 'form'));

    }

    public function viewOTSEval(Request $request, int $eval)
    {
        $eval = OTSEval::withAll()->find($eval);
        if (!$eval) {
            abort(404, "The OTS evaluation form is invalid.");
        }
        $student = $eval->student;
        if (!RoleHelper::isInstructor(Auth::user()->cid,
                $student->facility) && !RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, $student->facility)) {
            abort(403);
        }
        $positionSplit = explode('_', $eval->exam_position);
        $positionType = $positionSplit[count($positionSplit) - 1];
        $attempt = Helper::numToOrdinalWord(OTSEval::where([
            'student_id' => $eval->student_id,
            ['exam_date', '<=', $eval->exam_date],
            ['exam_position', 'like', '%' . $positionType]
        ])->count());
        $recs = TrainingRecord::where([
            'student_id' => $eval->student_id,
            ['session_date', '<=', $eval->exam_date],
            ['position', 'like', '%' . explode('_', $eval->exam_position)[1]],
            'ots_status' => 2
        ])->groupBy(['instructor_id'])->orderBy('session_date', 'desc')
            ->get()->pluck('instructor_id', 'session_date');

        return response()->view('mgt.controller.training.viewOtsEval',
            compact('student', 'eval', 'attempt', 'recs'));
    }

    public function viewEvals(Request $request)
    {
        if (!RoleHelper::isTrainingStaff(Auth::user()->cid, false)) {
            abort(403);
        }

        /** Training Records */
        $trainingfac = $request->input('fac', null);
        $facilities = Facility::active()->get();

        if (!$trainingfac) {
            if (RoleHelper::isVATUSAStaff()) {
                $trainingfac = "";
                $trainingfacname = "";
            } else {
                $trainingfac = Auth::user()->facility;
                $trainingfacname = Auth::user()->facility()->name;
            }
        } else {
            if (!RoleHelper::isVATUSAStaff()) {
                abort(403);
            }
            if (Facility::find($trainingfac)) {
                $trainingfacname = Helper::facShtLng($trainingfac);
            } else {
                abort(500);
            }
        }
        $evals = $trainingfac ? Facility::find($trainingfac)->evaluations()->where('facility_id',
            $trainingfac)->get() : [];

        return view('mgt.training.evals',
            compact('evals', 'trainingfac', 'trainingfacname', 'facilities'));
    }

    public function viewOTSEvalStatistics(Request $request, int $form)
    {
        $form = OTSEvalForm::withAll()->find($form);
        if (!$form) {
            abort(404, "The OTS evaluation form is invalid.");
        }

        $instructor = $request->input('instructor', null);
        $facility = $request->input('facility', null);
        $facilities = Facility::active()->get();
        $interval = intval($request->input('interval', 15)); //Last num of tests
        if (!$interval) {
            abort(400);
        }
        if (!RoleHelper::isInstructor(Auth::user()->cid,
                $facility) || ($instructor && !RoleHelper::isInstructor($instructor, $facility))) {
            abort(403);
        }

        $hasGlobalAccess = RoleHelper::isVATUSAStaff();
        if (!$hasGlobalAccess) {
            $facility = Auth::user()->facilityObj;
        } else {
            if ($facility) {
                $facility = Facility::find($facility);
                if (!$facility) {
                    abort(404, "Facility not found.");
                }
            }
        }

        //Chart 1: Stacked Line, Num Pass and Fails per Month
        $colors = ['rgb(255, 99, 132)', 'rgb(75, 192, 192)'];
        $numPassFailsData = ['labels' => [], 'datasets' => []];
        $datasets = [];

        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $numPassFailsData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');
            $numPassFails = OTSEval::selectRaw("result, DATE_FORMAT(exam_date, '%Y-%m') as month")
                ->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'");
            if ($facility) {
                $numPassFails->where('facility_id', $facility->id);
            }
            if ($instructor) {
                $numPassFails->where('instructor_id', $instructor);
            }
            $numPassFails = $numPassFails->where('form_id', $form->id)->orderBy('month',
                'ASC')->get();
            //dd(str_replace_array('?', $numPassFails->getBindings(), $numPassFails->toSql()));
            //dd($hoursPerMonth->get()->toArray());
            $totalPass = $numPassFails->filter(function ($q) {
                return $q->result;
            })->count();
            $totalFail = $numPassFails->filter(function ($q) {
                return !$q->result;
            })->count();
            $datasets[0]['data'][] = $totalFail;
            $datasets[0]['label'] = 'Fail';
            $datasets[1]['data'][] = $totalPass;
            $datasets[1]['label'] = 'Pass';
        }
        foreach ($datasets as $k => $v) {
            $numPassFailsData['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                'borderColor' => $colors[$k]
            ];
        }

        //Chart 2: Stacked Bar, Number of Evaluations by INS per Month
        $evalsPerMonthDataIns = ['labels' => [], 'datasets' => []];
        $allIns = [];
        $datasets = [];
        $allIns = $facility ? Facility::getFacTrainingStaff($facility->id)['ins'] : [];
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $evalsPerMonthDataIns['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $evalsPerMonth = OTSEval::selectRaw("DATE_FORMAT(exam_date, '%Y-%m') AS month, instructor_id");
            if ($facility) {
                $evalsPerMonth->where('facility_id', $facility->id);
            }
            $evalsPerMonth = $evalsPerMonth->where('form_id',
                $form->id)->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->orderBy('month', 'ASC')->get();
            if ($facility && !$instructor) {
                foreach ($allIns as $ins) {
                    // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
                    //dd($hoursPerMonth->get()->toArray());

                    $datasets[$ins['cid']]['label'] = $ins['name'];
                    $datasets[$ins['cid']]['data'][] = $evalsPerMonth->filter(function ($e) use ($ins) {
                        return $e->instructor_id == $ins['cid'];
                    })->count();
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = $instructor ? $evalsPerMonth->filter(function ($e) use ($instructor) {
                    return $e->instructor_id == $instructor;
                })->count() : $evalsPerMonth->count();
            }
        }
        foreach ($datasets as $k => $v) {
            $evalsPerMonthDataIns['datasets'][] = [
                'label' => $v['label'],
                'data' => $v['data'],
                $facility && !$instructor ? 'borderColor' : 'backgroundColor' => Factory::create()->hexColor
            ];
        }
        //Table: INS Name (SL: Pass/Fail last 15 num of tests), Num Passes (30/60/90), Num Fails (30/60/90)
        $tableData = [];
        if ($facility) {
            for ($i = 0; $i < count($allIns); $i++) {
                $tableData[$i]['name'] = $allIns[$i]['name'];
                $tableData[$i]['sparkline'] = "";
                $evals = OTSEval::where('instructor_id', $allIns[$i]['cid'])
                    ->where('form_id', $form->id)->where('facility_id', $facility->id)
                    ->orderBy('exam_date', 'ASC')->limit(10)->pluck('result')->all();
                for ($k = 0; $k < count($evals); $k++) {
                    $tableData[$i]['sparkline'] .= ($evals[$k] == 1 ? 1 : -1) . ($k + 1 == count($evals) ? '' : ",");
                }

                for ($k = 30; $k <= 90; $k += 30) {
                    $evals = OTSEval::where([
                        'instructor_id' => $allIns[$i]['cid'],
                        'form_id' => $form->id,
                        'facility_id' => $facility->id,
                        ['exam_date', '>=', Carbon::now()->subDays($k)]
                    ])->get();
                    //if($allIns[$i]['cid'] == 1275302) dd(str_replace_array('?', $evals->getBindings(), $evals->toSql()));
                    //  else $evals = $evals->get();
                    $tableData[$i]['numPasses'][$k] = $evals->filter(function ($e) {
                        return $e->result;
                    })->count();
                    $tableData[$i]['numFails'][$k] = $evals->filter(function ($e) {
                        return !$e->result;
                    })->count();
                }
            }
        }

        return view('mgt.training.otsEvalStats',
            compact('form', 'instructor', 'facilities', 'interval', 'facility',
                'numPassFailsData', 'evalsPerMonthDataIns', 'allIns', 'tableData', 'hasGlobalAccess'));
    }
}
