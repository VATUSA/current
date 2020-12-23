<?php namespace App\Http\Controllers;

use App\Actions;
use App\ChecklistData;
use App\Checklists;
use App\Classes\cPanelHelper;
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
use Carbon\CarbonInterval;
use Faker\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\User;
use App\Facility;
use App\Classes\RoleHelper;
use App\Classes\EmailHelper;
use App\Classes\CertHelper;
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
                    $trainingfac = $user->facility()->id;
                    $trainingfacname = $user->facility()->name;
                }
            } else {
                if (Facility::find($trainingfac)) {
                    $trainingfacname = Helper::facShtLng($trainingfac);
                } else {
                    abort(500);
                }
            }
            $trainingRecords = $user->facility == Auth::user()->facility || $user->visits()->where('facility',
                Auth::user()->facility)->exists() || RoleHelper::isVATUSAStaff() ? $user->trainingRecords()->where('facility_id',
                $trainingfac)->get() : [];
            $canAddTR = RoleHelper::isTrainingStaff(Auth::user()->cid, true,
                    $user->facility) && $user->cid !== Auth::user()->cid;

            //Get INS at ARTCC
            $ins = ['ins' => [], 'mtr' => []];
            $users = User::where('facility', $user->facility)->where('rating', '>=', Helper::ratingIntFromShort("I1"))
                ->where('rating', '<=', Helper::ratingIntFromShort("I3"))->get();
            if ($users) {
                foreach ($users as $tUser) {
                    $ins['ins'][$tUser->cid] = $tUser->fullname();
                }
            }
            $users = Role::where('facility', $user->facility)->where('role', 'INS')->get();
            if ($users) {
                foreach ($users as $tUser) {
                    $ins['ins'][$tUser->cid] = Helper::nameFromCID($tUser->cid);
                }
            }
            $users = Role::where('facility', $user->facility)->where('role', 'MTR')->get();
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
            $log->log = "Removed from role '" . $r->title . "' by " . Auth::user()->fullname();
            $log->save();
            $r->delete();

            //Delete Email
            EmailHelper::setForward('vat' . str_replace('us', 'usa', strtolower($r->role)) . '@vatusa.net',
                'vatusa2@vatusa.net');

            /*foreach($previous as $email) {
                EmailHelper::deleteEmail($email);
            }*/
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

        //Add Email
        /*
        $previous = EmailHelper::forwardDestination('vat' . str_replace('us', 'usa', $role).'@vatusa.net');

        foreach($previous as $email) {
            EmailHelper::deleteEmail($email);
        }
        */
        $user = User::find($cid);
        $email = strtolower(substr($user->fname, 0, 1) . "." . $user->lname) . "@vatusa.net";
        EmailHelper::addEmail($email, env('APP_KEY'));
        EmailHelper::setForward('vat' . str_replace('us', 'usa', $role) . '@vatusa.net', $email);


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
                'No evaluation forms found. Please try again later or contact VATUSA12.');
        }
        if ($forms->count() !== 4) {
            return redirect('mgt/facility#mem')->with('error',
                'Insufficient evaluation forms found. Please try again later or contact VATUSA12.');
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
            (RoleHelper::isVATUSAStaff() || !in_array($record->ots_status, [1, 2])) &&
            (RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, $record->facility) ||
                (RoleHelper::isTrainingStaff(Auth::user()->cid, true, $record->facility)
                    && $record->instructor_id == Auth::user()->cid)));
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
            return redirect('/mgt/facility#mem')->with('error', 'The controller is not eligible for that evaluation.');
        }

        return response()->view('mgt.controller.training.otsEval', compact('student', 'form'));

    }

    public
    function viewOTSEval(
        Request $request,
        int $eval
    ) {
        $eval = OTSEval::withAll()->find($eval);
        if (!$eval) {
            abort(404, "The OTS evaluation form is invalid.");
        }
        $student = $eval->student;
        if (!RoleHelper::isInstructor(Auth::user()->cid, $student->facility) && !RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, $student->facility)) {
            abort(403);
        }
        $attempt = Helper::numToOrdinalWord(OTSEval::where([
            'student_id' => $eval->student_id,
            ['exam_date', '<=', $eval->exam_date],
            ['exam_position', 'like', '%' . explode('_', $eval->exam_position)[1]]
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

    public
    function viewTrainingStatistics(
        Request $request
    ) {
        if (!RoleHelper::isTrainingStaff(Auth::user()->cid, false)) {
            abort(403);
        }

        $globalAccess = RoleHelper::isVATUSAStaff();

        $instructor = $request->input('instructor', null);
        $facility = $request->input('facility', null);
        //$region = $request->input('region', null);
        $region = null;
        $interval = $request->input('interval', 30);
        $facilities = Facility::active()->get();

        if (!$globalAccess) {
            $facility = Auth::user()->facilityObj->id;
        }

        /** Summary */

        $colors = [];

        //Total Session Time
        $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($interval));
        if ($region) {
            $records->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $records->where('facility_id', $facility);
        }

        $totalTime = $records->sum(DB::raw('TIME_TO_SEC(duration)'));
        $hours = floor($totalTime / 3600);
        $minutes = floor(($totalTime / 60) % 60);
        if (!$hours) {
            $sumTotalTimeStr = $minutes . " minute" . ($minutes !== 1 ? 's' : '');
        } else {
            $sumTotalTimeStr = "$hours hour" . ($hours !== 1 ? 's' : '') . ", " . $minutes . " minute" . ($minutes !== 1 ? 's' : '');
        }
        $sumTotalSessions = $records->count();

        //Average Time and Sessions per Week
        $avgTime = $records->selectRaw('SUM(TIME_TO_SEC(duration)) as total')
            ->groupBy([DB::raw("DATE_FORMAT(session_date, '%U')")])->pluck('total')->all();
        $avgTime = !empty($avgTime) ? array_sum($avgTime) / count($avgTime) : 0;

        $hours = floor($avgTime / 3600);
        $minutes = floor(($avgTime / 60) % 60);
        if (!$hours) {
            $sumAvgTimeStr = $minutes . " minute" . ($minutes !== 1 ? 's' : '');
        } else {
            $sumAvgTimeStr = "$hours hour" . ($hours !== 1 ? 's' : '') . ", " . $minutes . " minute" . ($minutes !== 1 ? 's' : '');
        }
        $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($interval));
        if ($region) {
            $records->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $records->where('facility_id', $facility);
        }
        $sumAvgSessions = $records->selectRaw('COUNT(*) AS total')
            ->groupBy([DB::raw("DATE_FORMAT(session_date, '%U')")])->pluck('total')->all();
        $sumAvgSessions = !empty($sumAvgSessions) ? round(array_sum($sumAvgSessions) / count($sumAvgSessions), 2) : 0;

        //Pass Rate
        $evals = OTSEval::where('exam_date', '>', Carbon::now()->subDays($interval));
        if ($region) {
            $evals = $evals->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $evals = $evals->where('facility_id', $facility);
        }
        $sumNumEvals = $evals->count();
        $sumNumPass = $sumNumEvals ? $evals->where('result', 1)->count() : 0;
        $sumNumFail = $sumNumEvals ? $sumNumEvals - $sumNumPass : 0;
        $sumPassRate = $sumNumEvals ? round($sumNumPass / $sumNumEvals * 100) : 0;

        /** INS/MTR Activity */

        //INS/MTR List
        $insWithSparklines = ['ins' => [], 'mtr' => []];
        $insActivity = [];
        $users = User::where('rating', '>=', Helper::ratingIntFromShort("I1"))
            ->where('rating', '<=', Helper::ratingIntFromShort("I3"))
            ->where('flag_homecontroller', 1)
            ->where('facility', '!=', 'ZZN');
        if ($facility) {
            $users->where('facility', $facility);
        }
        $users = $users->get();
        if ($users) {
            foreach ($users as $user) {
                $promo = Promotions::where([
                    'cid' => $user->cid,
                    'to'  => $user->rating
                ])->orderBy('created_at', 'desc')
                    ->first();
                $promoDate = $promo ? $promo->created_at->format('m/d/Y') : 'N/A';
                $insWithSparklines['ins'][] = [
                    'cid'       => $user->cid,
                    'sparkline' => $user->getTrainingActivitySparkline(),
                    'name'      => $user->fullname(true),
                    'since'     => $promoDate
                ];
            }
        }
        $users = Role::where('role', 'INS');
        if ($facility) {
            $users->where('facility', $facility);
        }
        $users = $users->get();
        if ($users) {
            foreach ($users as $user) {
                $insWithSparklines['ins'][] = [
                    'cid'       => $user->cid,
                    'sparkline' => $user->user->getTrainingActivitySparkline(),
                    'name'      => $user->user->fullname(true),
                    'since'     => $user->created_at->format('m/d/Y')
                ];
            }
        }
        $users = Role::where('role', 'MTR');
        if ($facility) {
            $users->where('facility', $facility);
        }
        $users = $users->get();
        if ($users) {
            foreach ($users as $user) {
                $insWithSparklines['mtr'][] = [
                    'cid'       => $user->cid,
                    'sparkline' => $user->user->getTrainingActivitySparkline(),
                    'name'      => $user->user->fullname(true),
                    'since'     => $user->created_at->format('m/d/Y')
                ];
            }
        }
        foreach ($insWithSparklines as $k => $v) {
            usort($insWithSparklines[$k], function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        //Hours per Month
        $hoursPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];
        $allIns = Facility::getFacTrainingStaff($facility);
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $hoursPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $hoursPerMonth = TrainingRecord::with(['instructor:cid,fname,lname'])->selectRaw("SUM(TIME_TO_SEC(duration)) AS sum, instructor_id, DATE_FORMAT(session_date, '%Y-%m') AS month");
            if ($region) {
                $hoursPerMonth = $hoursPerMonth->whereIn('facility_id',
                    Facility::where('region', $region)->get()->pluck('id')->all());
            } elseif ($facility) {
                $hoursPerMonth = $hoursPerMonth->where('facility_id', $facility);
            }
            $hoursPerMonth = $hoursPerMonth->where('session_date', '>',
                Carbon::parse('first day of this month')->subMonths(6))->whereRaw("DATE_FORMAT(session_date, '%Y-%m') = '$month'")->groupBy([
                'month',
                'instructor_id'
            ])->orderBy('month', 'ASC')->get();
            //dd(str_replace_array('?', $hoursPerMonth->getBindings(), $hoursPerMonth->toSql()));
            //dd($hoursPerMonth->get()->toArray());

            if ($facility) {
                foreach ($allIns as $type => $arr) {
                    foreach ($arr as $ins) {
                        $datasets[$ins['cid']]['label'] = $ins['name'];
                        $filter = $hoursPerMonth->filter(function ($q) use ($ins) {
                            return $q->instructor_id == $ins['cid'];
                        });
                        $datasets[$ins['cid']]['data'][] = $filter->count() ? floor($filter->first()->sum / 3600) : 0;
                    }
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = floor($hoursPerMonth->pluck('sum')->sum() / 3600);
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $hoursPerMonthData['datasets'][] = [
                'label'           => $v['label'],
                'data'            => $v['data'],
                'backgroundColor' => $colors[$k]
            ];
        }
        //Time per Instructor
        $timePerInstructorData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        if ($facility) {
            $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($interval));
            if ($region) {
                $records = $records->whereIn('facility_id',
                    Facility::where('region', $region)->get()->pluck('id')->all());
            } elseif ($facility) {
                $records = $records->where('facility_id', $facility);
            }
            $timePerInstructorData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
            $timePerInstructor = $records->with(['instructor:cid,fname,lname'])->selectRaw('SUM(TIME_TO_SEC(duration)) AS total, instructor_id')
                ->groupBy(['instructor_id']);
            foreach ($timePerInstructor->get() as $time) {
                if (!$time->instructor_id) {
                    continue;
                }
                $timePerInstructorData['labels'][] = $time->instructor->fullname();
                $timePerInstructorData['datasets'][0]['data'][] = floor($time->total / 3600);
                $timePerInstructorData['datasets'][0]['backgroundColor'][] = $colors[$time->instructor->cid] ?? Factory::create()->hexColor;
            }
        }

        //Table Data
        $i = 0;
        //dd($insWithSparklines);
        foreach ($insWithSparklines as $type => $v) {
            foreach ($v as $staff) {
                $insActivity[$i]['name'] = $staff['name'];
                $insActivity[$i]['sparkline'] = $staff['sparkline'];
                $insActivity[$i]['role'] = strtoupper($type);
                $insActivity[$i]['since'] = $staff['since'];
                for ($k = 30; $k <= 90; $k += 30) {
                    $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($k))
                        ->where('instructor_id', $staff['cid']);
                    if ($region) {
                        $records->whereIn('facility_id',
                            Facility::where('region', $region)->get()->pluck('id')->all());
                    } elseif ($facility) {
                        $records->where('facility_id', $facility);
                    }
                    $avgTime = $records->selectRaw('SUM(TIME_TO_SEC(duration)) as total')
                        ->groupBy([DB::raw("DATE_FORMAT(session_date, '%U')")])->pluck('total')->all();
                    if (!count($avgTime)) {
                        $avgTimeStr = "<em>No Sessions</em>";
                    } else {
                        $avgTime = array_sum($avgTime) / count($avgTime);

                        $hours = floor($avgTime / 3600);
                        $minutes = floor(($avgTime / 60) % 60);
                        if (!$hours) {
                            $avgTimeStr = $minutes . " minute" . ($minutes !== 1 ? 's' : '');
                        } else {
                            $avgTimeStr = "$hours hour" . ($hours !== 1 ? 's' : '') . ", " . $minutes . " minute" . ($minutes !== 1 ? 's' : '');
                        }
                    }
                    $insActivity[$i]['avgTime'][$k] = $avgTimeStr;

                    $records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($k))
                        ->where('instructor_id', $staff['cid']);
                    if ($region) {
                        $records->whereIn('facility_id',
                            Facility::where('region', $region)->get()->pluck('id')->all());
                    } elseif ($facility) {
                        $records->where('facility_id', $facility);
                    }
                    $avgSessions = $records->selectRaw('COUNT(*) AS total')
                        ->groupBy([DB::raw("DATE_FORMAT(session_date, '%U')")])->pluck('total')->all();
                    if (!count($avgSessions)) {
                        $avgSessions = "<em>No Sessions</em>";
                    } else {
                        $avgSessions = round(array_sum($avgSessions) / count($avgSessions), 2);
                    }

                    $insActivity[$i]['avgSessions'][$k] = $avgSessions;

                    /*$records = TrainingRecord::where('session_date', '>', Carbon::now()->subDays($k))
                        ->where('instructor_id', $staff['cid']);
                    if ($region) {
                        $records->whereIn('facility_id',
                            Facility::where('region', $region)->get()->pluck('id')->all());
                    } elseif ($facility) {
                        $records->where('facility_id', $facility);
                    }

                    $insActivity[$i]['numSessions'][$k] = $records->count();*/
                }
                $i++;
            }
        }

        /** OTS Evaluations */

        //Evals Conducted per Month
        $colors = [];
        $evalsPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];

        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $evalsPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $evalsPerMonth = OTSEval::with('form:id,name')->selectRaw("form_id, DATE_FORMAT(exam_date, '%Y-%m') AS month");
            if ($region) {
                $evalsPerMonth->whereIn('facility_id',
                    Facility::where('region', $region)->get()->pluck('id')->all());
            } elseif ($facility) {
                $evalsPerMonth->where('facility_id', $facility);
            }
            if ($instructor) {
                $evalsPerMonth->where('instructor_id', $instructor);
            }
            $evalsPerMonth = $evalsPerMonth->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->get();
            $k = 0;
            foreach (OTSEvalForm::active()->noStatements()->get() as $form) {
                $datasets[$k]['data'][] = $evalsPerMonth->filter(function ($e) use ($form) {
                    return $e->form->id == $form->id;
                })->count();
                $datasets[$k++]['label'] = $form->name;
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $evalsPerMonthData['datasets'][] = [
                'label'           => $v['label'],
                'data'            => $v['data'],
                'backgroundColor' => $colors[$k]
            ];
        }

        //Completed Evaluations per Form
        $evals = OTSEval::where('exam_date', '>=', Carbon::now()->subDays($interval));
        if ($region) {
            $evals->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $evals->where('facility_id', $facility);
        }
        $evalsPerFormData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        $evalsPerForm = $evals->with(['form:id,name'])->selectRaw('COUNT(*) AS total, form_id')
            ->groupBy([DB::raw('form_id')]);
        foreach ($evalsPerForm->get() as $eval) {
            if (!$eval->form_id) {
                continue;
            }
            $evalsPerFormData['labels'][] = $eval->form->name;
            $evalsPerFormData['datasets'][0]['data'][] = $eval->total;
            $evalsPerFormData['datasets'][0]['backgroundColor'][] = $colors[$eval->form_id] ?? Factory::create()->hexColor;
        }

        //Table Data
        $evalFormsTable = [];
        $i = 0;
        $evalForms = OTSEvalForm::active()->noStatements()->orderBy('rating_id')->get();
        foreach ($evalForms as $form) {
            $evalFormsTable[$i]['name'] = $form->name;
            $evalFormsTable[$i]['id'] = $form->id;
            $evalFormsTable[$i]['sparkline'] = $form->getStatSparkline($region, $facility);

            for ($k = 30; $k <= 90; $k += 30) {
                $completed = $form->evaluations()->where('exam_date', '>=', Carbon::now()->subDays($k));
                if ($region) {
                    $completed->whereIn('facility_id',
                        Facility::where('region', $region)->get()->pluck('id')->all());
                } elseif ($facility) {
                    $completed->where('facility_id', $facility);
                }
                $numConducted = $completed->count();
                if (!$numConducted) {
                    $passRate = '<em>N/A</em>';
                    $numPass = $numFail = 0;
                } else {
                    $numPass = $completed->where('result', 1)->count();
                    $numFail = $numConducted - $numPass;
                    $passRate = floor($numPass / $numConducted * 100);
                }
                $evalFormsTable[$i]['passRate'][$k] = $passRate;
                $evalFormsTable[$i]['numPass'][$k] = $numPass;
                $evalFormsTable[$i]['numFail'][$k] = $numFail;
                $evalFormsTable[$i]['numConducted'][$k] = $numConducted;
            }
            $i++;
        }
        //Evals Conducted per Month - INS
        $evalsPerMonthDataIns = ['labels' => [], 'datasets' => []];
        $evalsPerFormDataIns = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        if ($facility) {
            $colors = [];
            $datasets = [];
            for ($i = 6; $i >= 0; $i--) {
                $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
                $evalsPerMonthDataIns['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

                $evalsPerMonth = OTSEval::with('instructor:cid,fname,lname')->selectRaw("instructor_id, DATE_FORMAT(exam_date, '%Y-%m') AS month");
                if ($region) {
                    $evalsPerMonth->whereIn('facility_id',
                        Facility::where('region', $region)->get()->pluck('id')->all());
                }

                $evalsPerMonth->where('facility_id', $facility);
                $evalsPerMonth = $evalsPerMonth->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'")->orderBy('month',
                    'ASC')->get();
                // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
                //dd($hoursPerMonth->get()->toArray());
                foreach ($allIns['ins'] as $ins) {
                    $datasets[$ins['cid']]['label'] = $ins['name'];
                    $filter = $evalsPerMonth->filter(function ($q) use ($ins) {
                        return $q->instructor_id == $ins['cid'];
                    });
                    $datasets[$ins['cid']]['data'][] = $filter->count();
                }
            }
            foreach ($datasets as $k => $v) {
                $colors[$k] = Factory::create()->hexColor;
                $evalsPerMonthDataIns['datasets'][] = [
                    'label'           => $v['label'],
                    'data'            => $v['data'],
                    'backgroundColor' => $colors[$k]
                ];
            }

            //Completed Evaluations per Form - INS
            $evals = OTSEval::where('exam_date', '>=', Carbon::now()->subDays($interval));
            if ($region) {
                $evals->whereIn('facility_id',
                    Facility::where('region', $region)->get()->pluck('id')->all());
            } elseif ($facility) {
                $evals->where('facility_id', $facility);
            }
            $evalsPerForm = $evals->with(['instructor:cid,fname,lname'])->selectRaw('COUNT(*) AS total, instructor_id')
                ->groupBy(['instructor_id']);
            foreach ($evalsPerForm->get() as $eval) {
                if (!$eval->instructor_id) {
                    continue;
                }
                $evalsPerFormDataIns['labels'][] = $eval->instructor->fullname();
                $evalsPerFormDataIns['datasets'][0]['data'][] = $eval->total;
                $evalsPerFormDataIns['datasets'][0]['backgroundColor'][] = $colors[$eval->instructor->cid] ?? Factory::create()->hexColor;
            }
        }

        /** Training Records */
        $colors = [];
        $recordsPerMonthData = ['labels' => [], 'datasets' => []];
        $datasets = [];
        $recordsPerMonth = TrainingRecord::selectRaw("COUNT(*) AS total, position, DATE_FORMAT(session_date, '%Y-%m') AS month");
        if ($region) {
            $recordsPerMonth->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $recordsPerMonth->where('facility_id', $facility);
        }
        $recordsPerMonth->whereRaw("DATE_FORMAT(session_date, '%Y-%m') != DATE_FORMAT(NOW(), '%Y-%m')")->groupBy([
            'month',
            'position'
        ])->orderBy('month', 'ASC');
        // dd(str_replace_array('?', $evalsPerMonth->getBindings(), $evalsPerMonth->toSql()));
        //dd($hoursPerMonth->get()->toArray());
        $recordsPerMonth = TrainingRecord::selectRaw("position, DATE_FORMAT(session_date, '%Y-%m') AS month");
        if ($region) {
            $recordsPerMonth->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $recordsPerMonth->where('facility_id', $facility);
        }
        $recordsPerMonth->whereRaw("session_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)")->orderBy('month', 'ASC');
        $allPos = $recordsPerMonth->get()->pluck('position')->unique()->all();
        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $recordsPerMonthData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');

            $recordsPerMonth = TrainingRecord::selectRaw("position, DATE_FORMAT(session_date, '%Y-%m') AS month");
            if ($region) {
                $recordsPerMonth->whereIn('facility_id',
                    Facility::where('region', $region)->get()->pluck('id')->all());
            } elseif ($facility) {
                $recordsPerMonth->where('facility_id', $facility);
            }
            $recordsPerMonth = $recordsPerMonth->whereRaw("DATE_FORMAT(session_date, '%Y-%m') = '$month'")->orderBy('month',
                'ASC');
            if ($facility) {
                foreach ($allPos as $pos) {
                    $datasets[$pos]['data'][] = $recordsPerMonth->get()->filter(function ($q) use ($pos) {
                        return $q->position === $pos;
                    })->count();
                    $datasets[$pos]['label'] = $pos;
                }
            } else {
                $datasets[0]['label'] = "Total";
                $datasets[0]['data'][] = $recordsPerMonth->count();
            }
        }
        foreach ($datasets as $k => $v) {
            $colors[$k] = Factory::create()->hexColor;
            $recordsPerMonthData['datasets'][] = [
                'label'       => $v['label'],
                'data'        => $v['data'],
                'borderColor' => $colors[$k]
            ];
        }

        //Records per Type
        $records = TrainingRecord::where('session_date', '>=', Carbon::now()->subDays($interval));
        if ($region) {
            $records->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $records->where('facility_id', $facility);
        }
        $recordsPerTypeData = ['labels' => [], 'datasets' => [['data' => [], 'backgroundColor' => []]]];
        $recordsPerType = $records->selectRaw('COUNT(*) AS total, position')
            ->groupBy(['position']);
        foreach ($recordsPerType->get() as $record) {
            if (!$record->total) {
                continue;
            }
            $recordsPerTypeData['labels'][] = $record->position;
            $recordsPerTypeData['datasets'][0]['data'][] = $record->total;
            $recordsPerTypeData['datasets'][0]['backgroundColor'][] = $colors[$record->position] ?? Factory::create()->hexColor;
        }

        //Table Data
        $trainingRecords = TrainingRecord::with(['instructor:cid,fname,lname', 'student:cid,fname,lname']);
        if ($region) {
            $trainingRecords->whereIn('facility_id',
                Facility::where('region', $region)->get()->pluck('id')->all());
        } elseif ($facility) {
            $trainingRecords->where('facility_id', $facility);
        }
        $trainingRecords = $trainingRecords->get();

        return view('mgt.training.stats',
            compact('instructor', 'facility', 'region',
                'sumTotalSessions', 'sumTotalTimeStr', 'sumAvgTimeStr', 'sumAvgSessions',
                'sumNumPass', 'sumNumFail', 'sumPassRate',
                'hoursPerMonthData', 'timePerInstructorData', 'insActivity',
                'evalsPerMonthData', 'evalsPerFormData', 'evalsPerFormDataIns',
                'evalsPerMonthDataIns', 'evalFormsTable',
                'recordsPerTypeData', 'recordsPerMonthData', 'trainingRecords', 'facilities'));
    }

    public
    function viewEvals(
        Request $request
    ) {
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

    public
    function viewOTSEvalStatistics(
        Request $request,
        int $form
    ) {
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
        } elseif ($facility) {
            $facility = Facility::find($facility);
            if (!$facility) {
                abort(404, "Facility not found.");
            }
        }

        //Chart 1: Stacked Line, Num Pass and Fails per Month
        $colors = ['rgb(255, 99, 132)', 'rgb(75, 192, 192)'];
        $numPassFailsData = ['labels' => [], 'datasets' => []];
        $datasets = [];

        for ($i = 6; $i >= 0; $i--) {
            $month = Carbon::parse('first day of this month')->subMonths($i)->format('Y-m');
            $numPassFailsData['labels'][] = Carbon::parse('first day of this month')->subMonths($i)->format('F');
            $numPassFails = OTSEval::selectRaw("result, DATE_FORMAT(exam_date, '%Y-%m') as month")->whereRaw("DATE_FORMAT(exam_date, '%Y-%m') = '$month'");
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
                'label'       => $v['label'],
                'data'        => $v['data'],
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
                'label'                                                       => $v['label'],
                'data'                                                        => $v['data'],
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
                        'form_id'       => $form->id,
                        'facility_id'   => $facility->id,
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
