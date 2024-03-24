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

    public function isVATUSAStaff(): bool {
        return $this->isVATUSAStaff;
    }

    public function isWebTeam(): bool {
        return $this->isVATUSAWebTeam;
    }
    public function isFacilityStaff($facility = null): bool {
        $staffFacilities = $this->atmFacilities +
            $this->taFacilities +
            $this->ecFacilities +
            $this->feFacilities +
            $this->wmFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public function isFacilitySeniorStaff($facility = null): bool {
        $staffFacilities = $this->atmFacilities + $this->taFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public function isFacilityATMOrDATM($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->atmFacilities) > 0;
        } else {
            return in_array($facility, $this->atmFacilities);
        }
    }

    public function isTrainingAdministrator($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->taFacilities) > 0;
        } else {
            return in_array($facility, $this->taFacilities);
        }
    }

    public function isEventCoordinator($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->ecFacilities) > 0;
        } else {
            return in_array($facility, $this->ecFacilities);
        }

    }

    public function isFacilityEngineer($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->feFacilities) > 0;
        } else {
            return in_array($facility, $this->feFacilities);
        }

    }

    public function isWebmaster($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->wmFacilities) > 0;
        } else {
            return in_array($facility, $this->wmFacilities);
        }
    }

    public function isTrainingStaff($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        $staffFacilities = $this->taFacilities + $this->instructorFacilities + $this->mentorFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public function isMentor($facility = null): bool {
        if ($facility == null) {
            return count($this->mentorFacilities) > 0;
        } else {
            return in_array($facility, $this->mentorFacilities);
        }
    }

    public function isInstructor($facility = null): bool {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->instructorFacilities) > 0;
        } else {
            return in_array($facility, $this->instructorFacilities);
        }
    }
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
    private static function aclFlagsNew(int $cid): ACLFlags {
        if (array_key_exists($cid, self::$aclFlagsMap)) {
            return self::$aclFlagsMap[$cid];
        }
        $user = User::where('cid', $cid)::with('roles')->first();
        $flags = self::calculateACLFlags($user);
        self::$aclFlagsMap[$user->cid] = $flags;
        return $flags;
    }

    public static function authACL(): ACLFlags {
        if (!Auth::check()) {
            return new ACLFlags();
        }
        $user = Auth::user();
        return self::aclFlagsNew($user->cid);
    }

    public static function cidACL($cid): ACLFlags {
        return self::aclFlagsNew($cid);
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
}