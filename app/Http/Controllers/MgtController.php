<?php namespace App\Http\Controllers;

use App\Actions;
use App\ChecklistData;
use App\Checklists;
use App\Classes\Helper;
use App\Classes\PromoHelper;
use App\Classes\SMFHelper;
use App\OTSEval;
use App\OTSEvalForm;
use App\Promotions;
use App\Role;
use App\SoloCert;
use App\TrainingRecord;
use App\Transfers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\User;
use App\Facility;
use App\Classes\RoleHelper;
use App\Classes\EmailHelper;
use App\Classes\CertHelper;
use Auth;

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
        if (!RoleHelper::isMentor() && !RoleHelper::isInstructor() && !RoleHelper::isFacilitySeniorStaff() && !RoleHelper::isVATUSAStaff() && !RoleHelper::hasRole(Auth::user()->cid,
                Auth::user()->facility, "WM")) {
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

            /** Training Records */
            $trainingfac = $request->input('fac', null);
            $trainingfaclist = $user->trainingRecords()->groupBy('facility_id')->get();

            if (!$trainingfac) {
                if ($trainingfaclist->count() == 1) {
                    $trainingfac = $trainingfaclist->first()->facility_id;
                    $trainingfacname = Helper::facShtLng($trainingfac);
                } else {
                    $trainingfac = "ZHQ";
                    $trainingfacname = "";
                }
            } else {
                if (Facility::find($trainingfac)) {
                    $trainingfacname = Helper::facShtLng($trainingfac);
                } else {
                    abort(500);
                }
            }
            $trainingRecords = $user->facility == Auth::user()->facility || RoleHelper::isVATUSAStaff() ? $user->trainingRecords()->where('facility_id',
                $trainingfac)->get() : [];
            $canAddTR = RoleHelper::isTrainingStaff(Auth::user()->cid, true, $trainingfac,
                    false) && $user->cid !== Auth::user()->cid;

            //Get INS at ARTCC
            $ins = ['ins' => [], 'mtr' => []];
            $users = User::where('facility', $trainingfac)->where('rating', '>=', Helper::ratingIntFromShort("I1"))
                ->where('rating', '<=', Helper::ratingIntFromShort("I3"))->get();
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

            return view('mgt.controller.index',
                compact('user', 'checks', 'eligible', 'trainingRecords', 'trainingfaclist', 'trainingfac',
                    'trainingfacname', 'ins', 'canAddTR'));
        } else {
            return view('mgt.controller.404');
        }
    }

    public
    function getControllerMentor(
        $cid
    ) {
        if (!RoleHelper::isVATUSAStaff() && !RoleHelper::isFacilitySeniorStaff()) {
            return redirect('/mgt/controller/' . $cid)->with("error", "Access denied.");
        }

        $user = User::find($cid);
        if (!$user) {
            return redirect("/mgt/controller")->with("error", "User not found");
        }

        $role = Role::where("cid", $cid)->where("facility", $user->facility)->where("role", "MTR")->first();
        if (!$role) {
            $role = new Role();
            $role->cid = $user->cid;
            $role->role = "MTR";
            $role->facility = $user->facility;
            $role->save();
            $log = new Actions();
            $log->to = $user->cid;
            $log->log = "Mentor role for " . $user->facility . " added by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
            $log->save();

            return redirect("/mgt/controller/$cid")->with("success", "Successfully set as mentor");
        } else {
            $role->delete();
            $log = new Actions();
            $log->to = $user->cid;
            $log->log = "Mentor role for " . $user->facility . " deleted by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
            $log->save();

            return redirect("/mgt/controller/$cid")->with("success", "Successfully removed mentor role");
        }
    }

    /* Controller AJAX */
    public
    function getControllerTransfers(
        Request $request,
        $cid
    ) {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isInstructor() && !RoleHelper::isFacilityStaff() && !RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $transfers = Transfers::where('cid', $cid)->where('status', '<', 2)->orderBy('updated_at', 'ASC')->get();
        $data = [];
        foreach ($transfers as $transfer) {
            $temp = [
                'id'   => $transfer->id,
                'date' => substr($transfer->updated_at, 0, 10),
                'from' => $transfer->from,
                'to'   => $transfer->to
            ];
            $data[] = $temp;
        }
    }

    public
    function postControllerRating(
        Request $request,
        $cid
    ) {
        if (!$request->ajax()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(500);
        }

        $user = User::where('cid', $cid)->first();
        if (!$user) {
            abort(404);
        }
        if ($user->rating < Helper::ratingIntFromShort("C1") || $user->rating > Helper::ratingIntFromShort("I3")) {
            abort(401);
        }

        if (!is_numeric($request->input('rating'))) {
            abort(500);
        }

        $promo = new Promotions();
        $promo->cid = $cid;
        $promo->grantor = Auth::user()->cid;
        $promo->to = $request->input('rating');
        $promo->from = $user->rating;
        $promo->exam = "0000-00-00 00:00:00";
        $promo->examiner = Auth::user()->cid;
        $promo->position = "n/a";
        $promo->save();

        if (env('APP_ENV', 'dev') != "dev") {
            CertHelper::changeRating($cid, $request->input('rating'), true);
        }

        echo "1";

        return;
    }

    public
    function getControllerTransferWaiver(
        Request $request,
        $cid
    ) {
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
        $action->log = "Transfer Waiver " . (($user->flag_xferOverride == 1) ? "enabled" : "disabled") . " by " . Auth::user()->fullname() . " " . Auth::user()->cid;
        //$action->created_at = \DB::raw("NOW()");
        $action->save();

        echo $user->flag_xferOverride;

        return;
    }

    public
    function getControllerToggleBasic(
        $cid
    ) {
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
    public
    function getAce()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $roles = Role::where('role', 'ACE')->orderBy('cid')->get();

        return view('mgt.ace', ['roles' => $roles]);
    }

    public
    function deleteAce(
        Request $request,
        $cid
    ) {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $role = Role::where('cid', $cid)->where('role', 'ACE')->first();
        if ($role != null) {
            $role->delete();
        }

        SMFHelper::setPermissions($cid);

        return redirect("/mgt/ace");
    }

    public
    function putAce(
        Request $request
    ) {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $cid = $request->input('cid');

        if (!User::find($cid)) {
            // No user exits
            return redirect("/mgt/ace")->with('aceSubmit', 'The controller CID is invalid.');
        }
        if (Role::where('cid', $cid)->where('role', 'ACE')->first()) {
            return redirect("/mgt/ace")->with('aceSubmit', 'The controller is already a member of the team.');
        }

        if (Role::where('cid', $cid)->where('role', 'ACE')->count() == 0) {
            $role = new Role();
            $role->cid = $cid;
            $role->facility = "ZHQ";
            $role->role = "ACE";
            $role->created_at = Carbon::now();
            $role->save();
        }

        SMFHelper::setPermissions($cid);

        return redirect("/mgt/ace")->with('aceSubmit', true);
    }

    /*
     * Division Staff Management
     */
    public
    function getStaff()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        return view('mgt.staff');
    }

    public
    function deleteStaff(
        Request $request,
        $role
    ) {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }
        $roles = Role::where('role', $role)->where('facility', 'ZHQ')->get();
        foreach ($roles as $r) {
            $log = new Actions();
            $log->to = $r->cid;
            $log->log = "Removed from role '" . RoleHelper::roleTitle($role) . "' by " . Auth::user()->fullname();
            $log->save();
            $r->delete();
            SMFHelper::setPermissions($log->to);
        }
    }

    public
    function putStaff(
        Request $request,
        $role
    ) {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        parse_str(file_get_contents("php://input"), $vars);
        $cid = $vars['cid'];

        $this->deleteStaff($request, $role);

        $nrole = new Role();
        $nrole->cid = $cid;
        $nrole->role = $role;
        $nrole->facility = "ZHQ";
        $nrole->created_at = \Carbon\Carbon::now();
        $nrole->save();

        $log = new Actions();
        $log->to = $cid;
        $log->log = "Assigned to role '" . RoleHelper::roleTitle($role) . "' by " . Auth::user()->fullname();
        $log->save();

        if (config('staff.hq.moveToHQ') && $role != "US11") {
            $u = User::where('cid', $cid)->first();

            $tr = new \App\Transfers;
            $tr->cid = $cid;
            $tr->reason = "Auto Transfer to " . config('staff.hq.HQ') . ": Controller set as staff.";
            $tr->to = config('staff.hq.HQ');
            $tr->from = $u->facility;
            $tr->status = 1;
            $tr->actionby = 0;
            $tr->save();

            $log = new Actions;
            $log->to = $u->cid;
            $log->from = 0;
            $log->log = "Auto Transfer to " . $tr->to . ", controller set as staff.";
            $log->save();
            $u->addToFacility($tr->to);
        }
        SMFHelper::setPermissions($cid);
    }

    public
    function addLog(
        Request $request
    ) {
        if (!RoleHelper::isFacilitySeniorStaff() && !RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $this->validate($request, [
            'from' => 'required',
            'to'   => 'required',
            'log'  => 'required|min:1',
        ]);

        $le = new Actions;
        $le->to = $_POST['to'];
        $le->from = $_POST['from'];
        $le->log = $_POST['log'];
        $le->save();
        $le = Actions::where('id', $le->id)->first();

        return redirect('/mgt/controller/' . $le->to)->with('success', 'Your log entry has been added.');
    }

    public
    function getERR(
        Request $request
    ) {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        return view('mgt.err', ['cid' => $request->input("cid", '')]);
    }

    public
    function postERR(
        Request $request
    ) {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(401);
        }

        $cid = $request->input("cid");
        $reason = $request->input("reason");
        $facility = $request->input("facility");

        if (!$cid || !$reason || !$facility) {
            return redirect("/mgt/err")->with("error", "All items are required");
        }

        $user = User::find($cid);
        if (!$user) {
            return redirect("/mgt/err")->with("error", "User not found");
        }

        if (Transfers::where('cid', $cid)->where('status', 0)->count() > 0) {
            return redirect("/mgt/err")->with("error", "User has pending transfer request.");
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
        $log->log = "[Submitted by " . Auth::user()->fullname() . "] Requested transfer from " . $tr->from . " to " . $tr->to . ": " . $tr->reason;
        $log->save();

        EmailHelper::sendEmail([
            $tr->from . "-atm@vatusa.net",
            $tr->from . "-datm@vatusa.net",
            "vatusa" . $fac->region . "@vatusa.net"
        ], "Transfer Pending", "emails.transfers.internalpending", [
            'fname'    => $user->fname,
            'lname'    => $user->lname,
            'cid'      => $tr->cid,
            'facility' => $fac->id,
            'reason'   => $_POST['reason']
        ]);

        return redirect("/mgt/err")->with("success", "Transfer for $cid - " . $user->fullname() . " submitted.");
    }

    function getSolo()
    {
        if (!RoleHelper::isFacilitySeniorStaff() && !RoleHelper::isInstructor() && !RoleHelper::isVATUSAStaff()) {
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

        if (!preg_match("/^([A-Z0-9]{2,3})_(APP|CTR)$/i", $request->input("position"))) {
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

        $trainingRecordStatus = 0;
        $otsEvalStatus = 0;

        $dateOfExam = null;
        $examPosition = null;
        $evalId = null;

        $evals = $user->evaluations;
        $numPass = 0;
        $numFail = 0;

        if ($user->evaluations) {
            foreach ($user->evaluations as $eval) {
                if ($eval->form->rating_id == $user->rating + 1) {
                    if ($eval->result) {
                        $dateOfExam = $eval->exam_date;
                        $examPosition = $eval->exam_position;
                        $evalId = $eval->id;
                        $numPass++;
                    } else {
                        $numFail++;
                    }
                }
            }
            if ($numPass) {
                $otsEvalStatus = 1;
            } elseif ($numFail) {
                $otsEvalStatus = 2;
            }
        }

        switch (Helper::ratingShortFromInt($user->rating + 1)) {
            case 'S1':
                $pos = "GND";
                break;
            case 'S2':
                $pos = "TWR";
                break;
            case 'S3':
                $pos = "APP";
                break;
            case 'C1':
                $pos = "CTR";
                break;
            default:
                $pos = "NA";
                break;
        }
        if ($user->trainingRecords()->where([
            ['position', 'like', "%$pos"],
            'ots_status' => 1
        ])->exists()) {
            $trainingRecordStatus = 1;
        }

        if ($pos == "GND") {
            $trainingRecordStatus = $otsEvalStatus = -1;
        }

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
    public
    function getChecklists()
    {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $checklists = Checklists::orderBy('order', 'ASC')->get();

        return view('mgt.checklists.checklists', ['checklists' => $checklists]);
    }

    public
    function getChecklistItems(
        $id
    ) {
        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }
        $checklist = Checklists::find($id);
        if (!$checklist) {
            abort(404);
        }

        return view('mgt.checklists.checklist', ['cl' => $checklist]);
    }

    public
    function postChecklistsOrder()
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

    public
    function postChecklistItemsOrder()
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

    public
    function putChecklists()
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

    public
    function postChecklist(
        $id
    ) {
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

    public
    function deleteChecklist(
        $clid
    ) {
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

    public
    function putChecklistItem(
        $id
    ) {
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

    public
    function postChecklistItem(
        $clid,
        $id
    ) {
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

    public
    function deleteChecklistItem(
        $clid,
        $id
    ) {
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
    public
    function deleteActionLog(
        $log
    ) {
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

    public
    function toggleStaffPrevent(
        Request $request
    ) {
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

    public
    function toggleInsRole(
        Request $request
    ) {
        $cid = $request->cid;

        if (!RoleHelper::isVATUSAStaff()) {
            abort(403);
        }

        $user = User::findOrFail($cid);
        $facility = $user->facility;
        $currentIns = Role::where("facility", $facility)->where("cid", $cid)->where("role", "INS");
        if ($currentIns->count()) {
            //Delete role
            $currentIns->first()->delete();
            $log = new Actions();
            $log->to = $cid;
            $log->log = "Instructor role for " . $user->facility . " revoked by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
            $log->save();
        } else {
            //Create role
            $role = new Role();
            $role->cid = $cid;
            $role->facility = $facility;
            $role->role = "INS";
            $role->save();

            $log = new Actions();
            $log->to = $cid;
            $log->log = "Instructor role for " . $user->facility . " added by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
            $log->save();
        }

        return "1";
    }


    public
    function ajaxCanModifyRecord(
        $record
    ) {
        $record = TrainingRecord::find($record);
        if (!$record->count()) {
            return response()->json(false);
        }

        return response()->json(Auth::check() && $record->student_id != Auth::user()->cid &&
            !in_array($record->ots_status, [1, 2]) &&
            (RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, Auth::user()->facility, false, false) ||
                (RoleHelper::isTrainingStaff(Auth::user()->cid, true, $record->student->facility,
                        false) && $record->instructor_id == Auth::user()->cid)));
    }

    public
    function getOTSEval(
        Request $request,
        int $cid,
        $form = null
    ) {
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
            abort(400, "The controller is not eligible for that evaluation.");
        }

        return response()->view('mgt.controller.training.otsEval', compact('student', 'form'));

    }
    public
    function viewOTSEval (
        Request $request,
        int $cid,
       int $eval
    ) {
        $student = User::find($cid);
        if (!$student) {
            abort(404);
        }
        if (!RoleHelper::isInstructor(Auth::user()->cid, $student->facility)) {
            abort(403);
        }
        $eval = OTSEval::withAll()->find($eval);
        if (!$student || !$eval) {
            abort(404, "The OTS evaluation form is invalid.");
        }

        return response()->view('mgt.controller.training.viewOtsEval', compact('student', 'eval'));
    }
}
