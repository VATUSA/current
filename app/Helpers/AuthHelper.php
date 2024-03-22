<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ACLFlags {
    public $isVATUSAStaff = false;
    public $isVATUSAWebTeam = false;
    public $atmFacilities = [];
    public $taFacilities = [];
    public $ecFacilities = [];
    public $feFacilities = [];
    public $wmFacilities = [];
    public $mentorFacilities = [];
    public $instructorFacilities = [];

}
class AuthHelper
{
    public static $aclFlagsMap = [];
    public static function aclFlags(User $user): ACLFlags {
        if (array_key_exists($user->cid, self::$aclFlagsMap)) {
            return self::$aclFlagsMap[$user->cid];
        }
        $flags = self::calculateACLFlags($user);
        self::$aclFlagsMap[$user->cid] = $flags;
        return $flags;
    }

    private static function calculateACLFlags(User $user): ACLFlags {
        $flags = new ACLFlags();
        foreach ($user->roles()->get() as $role) {
            if (in_array($role->role, ['US1', 'US2', 'US3', 'US4', 'US5', 'US6', 'US7', 'US8', 'US9'])) {
                $flags->isVATUSAStaff = true;
            } else if ($role->role == 'USWT') {
                $flags->isVATUSAWebTeam = true;
            } else if ($role->role == 'ATM' || $role->role == 'DATM') {
                $flags->atmFacilities[] = $role->facility;
            } else if ($role->role == 'TA') {
                $flags->taFacilities[] = $role->facility;
            } else if ($role->role == "EC") {
                $flags->ecFacilities[] = $role->facility;
            } else if ($role->role == "FE") {
                $flags->feFacilities[] = $role->facility;
            } else if ($role->role == 'WM') {
                $flags->wmFacilities[] = $role->facility;
            } else if ($role->role == 'MTR') {
                $flags->mentorFacilities[] = $role->facility;
            } else if ($role->role == 'INS') {
                $flags->instructorFacilities[] = $role->facility;
            }
        }
        Log::debug(sprintf("Calculated ACL flags for %d - %s", $user->cid, var_export($flags, true)));
        return $flags;
    }
    public static function isVATUSAStaff(): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        return $flags->isVATUSAStaff;
    }

    public static function isWebTeam(): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        return $flags->isVATUSAWebTeam;
    }
    public static function isFacilityStaff($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        $staffFacilities = $flags->atmFacilities +
            $flags->taFacilities +
            $flags->ecFacilities +
            $flags->feFacilities +
            $flags->wmFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public static function isFacilitySeniorStaff($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        $staffFacilities = $flags->atmFacilities + $flags->taFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public static function isFacilityATMOrDATM($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->atmFacilities) > 0;
        } else {
            return in_array($facility, $flags->atmFacilities);
        }
    }

    public static function isTrainingAdministrator($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->taFacilities) > 0;
        } else {
            return in_array($facility, $flags->taFacilities);
        }
    }

    public static function isEventCoordinator($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->ecFacilities) > 0;
        } else {
            return in_array($facility, $flags->ecFacilities);
        }

    }

    public static function isFacilityEngineer($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->feFacilities) > 0;
        } else {
            return in_array($facility, $flags->feFacilities);
        }

    }

    public static function isWebmaster($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->wmFacilities) > 0;
        } else {
            return in_array($facility, $flags->wmFacilities);
        }
    }

    public static function isTrainingStaff($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        $staffFacilities = $flags->taFacilities + $flags->instructorFacilities + $flags->mentorFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public static function isMentor($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($facility == null) {
            return count($flags->mentorFacilities) > 0;
        } else {
            return in_array($facility, $flags->mentorFacilities);
        }
    }

    public static function isInstructor($facility = null): bool {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $flags = self::aclFlags($user);
        if ($flags->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($flags->instructorFacilities) > 0;
        } else {
            return in_array($facility, $flags->instructorFacilities);
        }
    }
}