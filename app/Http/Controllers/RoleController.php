<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Helpers\AuthHelper;
use App\Helpers\CobaltAPIHelper;
use App\Helpers\RoleHelperV2;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function getRoleList(Request $request, $fac = null) {
        if (!AuthHelper::authACL()->canManageRoles()) {
            abort(401);
        }
        if ($fac != null) {
            $roles = Role::where('facility', $fac)->orderBy('cid')->get();
        } else {

            $roles = Role::orderBy('cid')->get();
        }

        return view('mgt.roles', ['roles' => $roles]);
    }

    public function postAssignRole(Request $request) {
        $cid = $request->cid;
        $role = $request->role;
        $facility = $request->facility;

        if ($facility == "ZHQ" && $role == "CBT") {
            $facility = "ZAE";
        }

        $user = User::findOrFail($cid);

        if ($user->flag_preventStaffAssign) {
            abort(403);
        }

        if (!RoleHelperV2::canAssignRole($cid, $role, $facility)) {
            abort(403);
        }
        if (!RoleHelperV2::hasRole($user, $role, $facility)) {
            RoleHelperV2::assignRole($cid, $role, $facility);
        }
        return redirect("/mgt/controller/$cid#roles");
    }

    public function postRevokeRole(Request $request) {
        $cid = $request->cid;
        $role = $request->role;
        $facility = $request->facility;

        if ($facility == "ZHQ" && $role == "CBT") {
            $facility = "ZAE";
        }

        $user = User::findOrFail($cid);

        if (!RoleHelperV2::canAssignRole($cid, $role, $facility)) {
            abort(403);
        }
        if (RoleHelperV2::hasRole($user, $role, $facility)) {
            RoleHelperV2::revokeRole($cid, $role, $facility);
        }
        return redirect("/mgt/controller/$cid#roles");
    }

}