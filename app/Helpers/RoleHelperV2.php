<?php

namespace App\Helpers;

use App\Models\Actions;
use App\Models\Role;

class RoleHelperV2
{
    public static function assignRole(int $cid, string $role, string $facility) {
        $r = new Role();
        $r->cid = $cid;
        $r->facility = $facility;
        $r->role = $role;
        $r->save();

        $roleStr = $facility == "ZHQ" ? "VATUSA " . $role : $facility . " " . $role;
        $log = new Actions();
        $log->to = $cid;
        $log->log = $roleStr . " role assigned by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
        $log->save();
    }

    public static function revokeRole(int $cid, string $role, string $facility) {
        $currentRole = Role::where("facility", $facility)->where("cid", $cid)->where("role", $role)->first();
        $currentRole->delete();

        $roleStr = $facility == "ZHQ" ? "VATUSA " . $role : $facility . " " . $role;
        $log = new Actions();
        $log->to = $cid;
        $log->log = $roleStr . " role revoked by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
        $log->save();
    }

    // Assigns a role if not assigned, revokes that role if it is assigned
    public static function toggleRole(int $cid, string $role, string $facility) {
        $currentRole = Role::where("facility", $facility)->where("cid", $cid)->where("role", $role);
        if ($currentRole->count()) {
            RoleHelperV2::revokeRole($cid, $role, $facility);
        } else {
            RoleHelperV2::assignRole($cid, $role, $facility);
        }
    }
}