<?php namespace App\Http\Controllers;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\SMFHelper;
use App\Helpers\AuthHelper;
use App\Models\Role;
use Auth;
use App\Models\Transfers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Facility;
use App\Classes\RoleHelper;
use App\Models\Actions;
use Illuminate\Support\Facades\Cache;

class FacMgtController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //       $this->middleware('ins');
    }


    public function getIndex($fac = null)
    {
        if (!AuthHelper::authACL()->canViewFacilityRoster($fac)) {
            abort(403);
        }

        if ($fac === null) {
            if (\Auth::user()->facility == "ZHQ") {
                $fac = "HCF";
            } else {
                $fac = \Auth::user()->facility;
            }
        }

        if ($fac == "Winterfell") {
            return view('eastereggs.winterfell');
        }

        $facility = Facility::find($fac);

        $promotionEligible = Cache::get("promotionEligible-$fac") ?? "N/A";

        $staffPOCOptions = [];

        foreach (["ATM", "DATM", "TA", "EC", "FE", "WM"] as $role) {
            $staffPOCOptions[$role] = [];
            foreach (Role::where("facility", $fac)->where("role", $role)->get() as $userRole) {
                $staffPOCOptions[$role][$userRole->user->cid] = $userRole->user->fname . " " . $userRole->user->lname;
            }
        }

        return view('mgt.facility.index',
            [
                'fac' => $fac,
                'facility' => $facility,
                'promotionEligible' => $promotionEligible,
                'atm' => RoleHelper::getNameFromRole('ATM', $fac, 1),
                'datm' => RoleHelper::getNameFromRole('DATM', $fac, 1),
                'ta' => RoleHelper::getNameFromRole('TA', $fac, 1),
                'ec' => RoleHelper::getNameFromRole('EC', $fac, 1),
                'fe' => RoleHelper::getNameFromRole('FE', $fac, 1),
                'wm' => RoleHelper::getNameFromRole('WM', $fac, 1),
                'staffPOCOptions' => $staffPOCOptions,
            ]);
    }

    public function postAPIGenerate(Request $request, $facility)
    {
        if (!$request->ajax()) {
            abort(401);
        }
        $authACL = AuthHelper::authACL();
        if (!$authACL->canManageFacilityTechConfig($facility)) {
            abort(403);
        }

        $key = base64_encode(random_bytes(ceil(0.75 * 16)));
        $facility = Facility::find($facility);
        if ($facility->active != 1) {
            abort(500);
        }

        $facility->apikey = $key;
        $facility->save();

        echo $key;

        return;
    }

    public function postAPISandboxGenerate(Request $request, $facility)
    {
        if (!$request->ajax()) {
            abort(401);
        }
        $authACL = AuthHelper::authACL();
        if (!$authACL->canManageFacilityTechConfig($facility)) {
            abort(403);
        }

        $key = base64_encode(random_bytes(ceil(0.75 * 16)));
        $facility = Facility::find($facility);
        if ($facility->active != 1) {
            abort(500);
        }

        $facility->api_sandbox_key = $key;
        $facility->save();

        echo $key;

        return;
    }

    public function savePointsOfContact(Request $request, $fac)
    {
        if (!AuthHelper::authACL()->canManageFacilityStaff($fac)) {
            abort(401);
        }
        $facility = Facility::findOrFail($fac);

        $staffPOCOptions = [];

        foreach (["ATM", "DATM", "TA", "EC", "FE", "WM"] as $role) {
            $staffPOCOptions[$role] = [];
            foreach (Role::where("facility", $fac)->where("role", $role)->get() as $userRole) {
                $staffPOCOptions[$role][] = $userRole->user->cid;
            }
        }

        $atm = (int)$request->get('atm');
        $datm = (int)$request->get('datm');
        $ta = (int)$request->get('ta');
        $ec = (int)$request->get('ec');
        $fe = (int)$request->get('fe');
        $wm = (int)$request->get('wm');

        if (AuthHelper::authACL()->isVATUSAStaff() && ($atm == -1 || in_array($atm, $staffPOCOptions["ATM"]))) {
            if($facility->atm != $atm){
                $log = new Actions();
                $log->to = $facility->atm;
                $log->log = "Removed as " . $fac . " ATM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->atm = $atm;
                $log = new Actions();
                $log->to = $atm;
                $log->log = "Set as " . $fac . " ATM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        if (AuthHelper::authACL()->isVATUSAStaff() && ($datm == -1 || in_array($datm, $staffPOCOptions["DATM"]))) {
            if($facility->datm != $datm){
                $log = new Actions();
                $log->to = $facility->datm;
                $log->log = "Removed as " . $fac . " DATM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->datm = $datm;
                $log = new Actions();
                $log->to = $datm;
                $log->log = "Set as " . $fac . " DATM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        if (AuthHelper::authACL()->isVATUSAStaff() && ($ta == -1 || in_array($ta, $staffPOCOptions["TA"]))) {
            if($facility->ta != $ta){
                $log = new Actions();
                $log->to = $facility->ta;
                $log->log = "Removed as " . $fac . " TA by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->ta = $ta;
                $log = new Actions();
                $log->to = $ta;
                $log->log = "Set as " . $fac . " TA by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        if ($ec == -1 || in_array($ec, $staffPOCOptions["EC"])) {
            if($facility->ec != $ec){
                $log = new Actions();
                $log->to = $facility->ec;
                $log->log = "Removed as " . $fac . " EC by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->ec = $ec;
                $log = new Actions();
                $log->to = $ec;
                $log->log = "Set as " . $fac . " EC by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        if ($fe == -1 || in_array($fe, $staffPOCOptions["FE"])) {
            if($facility->fe != $fe){
                $log = new Actions();
                $log->to = $facility->fe;
                $log->log = "Removed as " . $fac . " FE by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->fe = $fe;
                $log = new Actions();
                $log->to = $fe;
                $log->log = "Set as " . $fac . " FE by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        if ($wm == -1 || in_array($wm, $staffPOCOptions["WM"])) {
            if($facility->wm != $wm){
                $log = new Actions();
                $log->to = $facility->wm;
                $log->log = "Removed as " . $fac . " WM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
                $facility->wm = $wm;
                $log = new Actions();
                $log->to = $wm;
                $log->log = "Set as " . $fac . " WM by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
                $log->save();
            }
        }
        $facility->save();
        return redirect("/mgt/facility/" . $fac);
    }

    public function deleteController(Request $request, $facility, $cid)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        if (!AuthHelper::authACL()->canManageFacilityRoster($facility)) {
            abort(403);
        }

        $user = User::find($cid);
        if (!$user) {
            abort(404);
        }

        parse_str(file_get_contents("php://input"), $vars);

        $user->removeFromFacility(Auth::user()->cid, $vars['reason']);
    }

    public function ajaxTransfers(Request $request, $status)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        $tr = Transfers::find($_POST['id']);
        if (!AuthHelper::authACL()->canManageFacilityRoster($tr->to)) {
            abort(403);
        }

        if (($status == 1 || $status == 2) && isset($_POST['id'])) {
            if ($status == 2 && isset($_POST['reason']) && !empty($_POST['reason'])) {
                if ($tr != null) {
                    $tr->reject(Auth::user()->cid, $_POST['reason']);

                    return 1;
                } else {
                    return 0;
                }

            }
            if ($status == 1) {
                if ($tr != null) {
                    $tr->accept(Auth::user()->cid);

                    return 1;
                } else {
                    return 0;
                }

            }
        }
    }

    public function ajaxTransferReason(Request $request)
    {
        if (!$request->ajax()) {
            abort(500);
        }
        //if (!RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "ATM") && !RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "DATM") && !RoleHelper::isVATUSAStaff()) abort(401);

        if (isset($_REQUEST['id'])) {
            $t = Transfers::where('id', $_REQUEST['id'])->count();
            if ($t) {
                $t = Transfers::where('id', $_REQUEST['id'])->first();

                return $t->reason;
            }
        }
    }
}
