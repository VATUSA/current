<?php

namespace App\Http\Controllers;

use App\Helpers\RoleHelperV2;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class RoleController extends Controller
{
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
        if (!RoleHelperV2::hasRole($cid, $role, $facility)) {
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
        if (RoleHelperV2::hasRole($cid, $role, $facility)) {
            RoleHelperV2::revokeRole($cid, $role, $facility);
        }

        return redirect("/mgt/controller/$cid#roles");
    }

}