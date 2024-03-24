<?php

namespace App\Helpers;

use App\Models\Policy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ACLFlags
{
    private $isVATUSAStaff = false;
    private $isVATUSAWebTeam = false;
    private $atmFacilities = [];
    private $taFacilities = [];
    private $ecFacilities = [];
    private $feFacilities = [];
    private $wmFacilities = [];
    private $mentorFacilities = [];
    private $instructorFacilities = [];

    public function __construct($user = null)
    {
        if ($user == null) {
            return;
        }
        foreach ($user->roles()->get() as $role) {
            if (in_array($role->role, ['US1', 'US2', 'US3', 'US4', 'US5', 'US6', 'US7', 'US8', 'US9'])) {
                $this->isVATUSAStaff = true;
            } else if ($role->role == 'USWT') {
                $this->isVATUSAWebTeam = true;
            } else if ($role->role == 'ATM' || $role->role == 'DATM') {
                $this->atmFacilities[] = $role->facility;
            } else if ($role->role == 'TA') {
                $this->taFacilities[] = $role->facility;
            } else if ($role->role == "EC") {
                $this->ecFacilities[] = $role->facility;
            } else if ($role->role == "FE") {
                $this->feFacilities[] = $role->facility;
            } else if ($role->role == 'WM') {
                $this->wmFacilities[] = $role->facility;
            } else if ($role->role == 'MTR') {
                $this->mentorFacilities[] = $role->facility;
            } else if ($role->role == 'INS') {
                $this->instructorFacilities[] = $role->facility;
            }
        }
        Log::debug(sprintf("Calculated ACL flags for %d - %s", $user->cid, var_export($this, true)));
    }

    public function isVATUSAStaff(): bool
    {
        return $this->isVATUSAStaff;
    }

    public function isWebTeam(): bool
    {
        return $this->isVATUSAWebTeam;
    }

    public function isFacilityStaff($facility = null): bool
    {
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

    public function isFacilitySeniorStaff($facility = null): bool
    {
        $staffFacilities = $this->atmFacilities + $this->taFacilities;
        if ($facility == null) {
            return count($staffFacilities) > 0;
        } else {
            return in_array($facility, $staffFacilities);
        }
    }

    public function isFacilityATMOrDATM($facility = null): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->atmFacilities) > 0;
        } else {
            return in_array($facility, $this->atmFacilities);
        }
    }

    public function isTrainingAdministrator($facility = null): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->taFacilities) > 0;
        } else {
            return in_array($facility, $this->taFacilities);
        }
    }

    public function isEventCoordinator($facility = null): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->ecFacilities) > 0;
        } else {
            return in_array($facility, $this->ecFacilities);
        }

    }

    public function isFacilityEngineer($facility = null): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->feFacilities) > 0;
        } else {
            return in_array($facility, $this->feFacilities);
        }

    }

    public function isWebmaster($facility = null): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if ($facility == null) {
            return count($this->wmFacilities) > 0;
        } else {
            return in_array($facility, $this->wmFacilities);
        }
    }

    public function isTrainingStaff($facility = null): bool
    {
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

    public function isMentor($facility = null): bool
    {
        if ($facility == null) {
            return count($this->mentorFacilities) > 0;
        } else {
            return in_array($facility, $this->mentorFacilities);
        }
    }

    public function isInstructor($facility = null): bool
    {
        if ($facility == null) {
            return count($this->instructorFacilities) > 0;
        } else {
            return in_array($facility, $this->instructorFacilities);
        }
    }


    public function canViewPolicy(Policy $policy): bool
    {
        if ($this->isVATUSAStaff) {
            return true;
        }
        if (!$policy->visible) {
            return false;
        }
        $perms = explode('|', $policy->perms);
        foreach ($perms as $perm) {
            $perm = intval($perm);
            if ($perm === Policy::PERMS_ALL) {
                return true;
            }
            if ($perm === Policy::PERMS_HOME && Auth::user()->flag_homecontroller) {
                return true;
            }
            if ($perm === Policy::PERMS_WM &&
                ($this->isWebmaster() || $this->isFacilityATMOrDATM())) {
                return true;
            }
            if ($perm === Policy::PERMS_FE &&
                ($this->isFacilityEngineer() || $this->isFacilityATMOrDATM())) {
                return true;
            }
            if ($perm === Policy::PERMS_EC &&
                ($this->isEventCoordinator() || $this->isFacilityATMOrDATM())) {
                return true;
            }
            if ($perm === Policy::PERMS_MTR &&
                ($this->isMentor() || $this->isInstructor() || $this->isFacilitySeniorStaff())) {
                return true;
            }
            if ($perm === Policy::PERMS_INS &&
                ($this->isInstructor() || $this->isFacilitySeniorStaff())) {
                return true;
            }
            if ($perm === Policy::PERMS_TA &&
                $this->isFacilitySeniorStaff()) {
                return true;
            }
            if ($perm === Policy::PERMS_DATM &&
                $this->isFacilityATMOrDATM()) {
                return true;
            }
            if ($perm === Policy::PERMS_ATM &&
                $this->isFacilityATMOrDATM()) {
                return true;
            }

        }

        return false;
    }
}