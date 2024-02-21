<?php

namespace App\Helpers;

use App\Classes\DiscordHelper;
use App\Classes\RoleHelper;
use App\Models\Actions;
use App\Models\Facility;
use App\Models\Role;
use App\Models\RoleTitle;
use Illuminate\Support\Facades\Auth;

class RoleHelperV2
{
    public static $globalRoles = ["ACE", "CBT", "USWT", "SMT", "DICE", "DCC"];
    public static $facilityRolesUSA = ["ATM", "DATM", "TA", "INS"];

    public static $facilityRolesATM = ["EC", "FE", "WM", "EMAIL"];
    public static $facilityRolesTA = ["MTR", "FACCBT"];

    public static function roleTitles($roles = null)
    {
        $out = [];
        foreach (RoleTitle::get() as $roleTitle) {
            if ($roles == null || in_array($roleTitle->role, $roles)) {
                $out[] = $roleTitle;
            }
        }
        return $out;
    }

    public static function assignedRoles(int $cid)
    {
        return Role::where('cid', $cid)->get();
    }

    public static function canAssignRole(int $cid, string $role, string $facility = null)
    {
        if (in_array($role, self::$globalRoles) || in_array($role, self::$facilityRolesUSA)) {
            return RoleHelper::isVATUSAStaff();
        }
        if (in_array($role, self::$facilityRolesATM)) {
            return RoleHelper::isFacilitySeniorStaffExceptTA(null, $facility);
        }
        if (in_array($role, self::$facilityRolesTA)) {
            return RoleHelper::isFacilitySeniorStaff(null, $facility);
        }
        return false; // Only allow groups in one of the above lists to be assigned
    }

    public static function hasRole(int $cid, string $role, string $facility)
    {
        return Role::where('role', $role)->where('cid', $cid)->where('facility', $facility)->exists();
    }

    public static function assignRole(int $cid, string $role, string $facility)
    {
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
        DiscordHelper::assignRoles($cid);
    }

    public static function revokeRole(int $cid, string $role, string $facility)
    {
        $currentRole = Role::where("facility", $facility)->where("cid", $cid)->where("role", $role)->first();
        $currentRole->delete();

        $roleStr = $facility == "ZHQ" ? "VATUSA " . $role : $facility . " " . $role;
        $log = new Actions();
        $log->to = $cid;
        $log->log = $roleStr . " role revoked by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ").";
        $log->save();
        DiscordHelper::assignRoles($cid);

        // Also remove from point of contact, if set
        $fac = Facility::where('id', $facility)->first();
        switch ($role) {
            case 'ATM':
                if ($cid == $fac->atm)
                    $fac->atm = 0;
                break;
            case 'DATM':
                if ($cid == $fac->datm)
                    $fac->datm = 0;
                break;
            case 'TA':
                if ($cid == $fac->ta)
                    $fac->ta = 0;
                break;
            case 'EC':
                if ($cid == $fac->ec)
                    $fac->ec = 0;
                break;
            case 'FE':
                if ($cid == $fac->fe)
                    $fac->fe = 0;
                break;
            case 'WM':
                if ($cid == $fac->wm)
                    $fac->wm = 0;
                break;
        }
    }

    // Assigns a role if not assigned, revokes that role if it is assigned
    public static function toggleRole(int $cid, string $role, string $facility)
    {
        if (RoleHelperV2::hasRole($cid, $role, $facility)) {
            RoleHelperV2::revokeRole($cid, $role, $facility);
        } else {
            RoleHelperV2::assignRole($cid, $role, $facility);
        }
    }
}