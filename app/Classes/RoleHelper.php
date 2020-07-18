<?php

namespace App\Classes;

use \App\Role;
use \App\User;
use \App\Facility;
use \App\RoleTitle;
use \App\Actions;
use \Auth;
use Illuminate\Support\Facades\Schema;

class RoleHelper
{
    //
    // getNameFromRole($role, $fac, $prim)
    // $role = "ATM", "USA11", etc.
    // $fac = 'ZNY', 'ZHQ', etc.
    // $prim = 1, 0 (Primary only?)
    //
    public static function getNameFromRole($role, $fac = 'ZHQ', $prim = 0)
    {
        $return = "";
        $res = Facility::where('id', $fac)->first();
        switch (strtoupper($role)) {
            case 'ATM':
                $u = $res->atm;
                break;
            case 'DATM':
                $u = $res->datm;
                break;
            case 'TA':
                $u = $res->ta;
                break;
            case 'EC':
                $u = $res->ec;
                break;
            case 'FE':
                $u = $res->fe;
                break;
            case 'WM':
                $u = $res->wm;
                break;
            default:
                $u = 0;
                break;
        }
        if ($u != 0) {
            $r = User::where('cid', $u)->count();
            if ($r) {
                $ur = User::where('cid', $u)->first();
                $return .= $ur->fname . ' ' . $ur->lname;
            }
        }
        if ($prim == 0) {
            $chk = Role::where('facility', $fac)->where('role', $role)->count();
            if ($chk) {
                $i = 0;
                $g = Role::where('facility', $fac)->where('role', $role)->get();
                foreach ($g as $ud) {
                    $ua = User::where('cid', $ud->cid)->count();
                    if ($ua) {
                        $ua = User::where('cid', $ud->cid)->first();
                        $i++;
                        if ($u != 0 || $i > 1) {
                            $return .= ", " . $ua->fullname();
                        } else {
                            $return .= $ua->fullname();
                        }
                    }
                }
            }
        }
        if (empty($return)) {
            return 'Vacant';
        }

        return $return;
    }

    public static function getUserRoleFull($cid, $fac)
    {
        if ($fac == "ZHQ") {
            $f = "";
        } else {
            $f = "$fac ";
        }

        return $f . static::getUserRole($cid, $fac);
    }

    public static function getUserRole($cid, $fac)
    {
        if ($fac == "ZHQ") {
            return 'Division Staff';
        }
        $res = Facility::where('atm', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Air Traffic Manager';
        }
        $res = Facility::where('datm', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Deputy Air Traffic Manager';
        }
        $res = Facility::where('ta', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Training Administrator';
        }
        $res = Facility::where('ec', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Events Coordinator';
        }
        $res = Facility::where('fe', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Facility Engineer';
        }
        $res = Facility::where('wm', $cid)->where('id', $fac)->count();
        if ($res) {
            return 'Webmaster';
        }
        $ud = User::where('cid', $cid)->first();
        if ($ud->urating->short == "I1") {
            return 'Instructor';
        }
        if ($ud->rating < Helper::ratingIntFromShort("C1")) {
            return "Student";
        }

        return 'Controller';
    }

    public static function roleTitle($role, $shrt = false)
    {
        if ($shrt) {
            $res = RoleTitle::where('title', $role)->count();
            if ($res) {
                $res = RoleTitle::where('title', $role)->first();

                return $res->role;
            } else {
                return "Unknown";
            }
        } else {
            $res = RoleTitle::where('role', $role)->count();
            if ($res) {
                $res = RoleTitle::where('role', $role)->first();

                return $res->title;
            } else {
                return "Unknown";
            }
        }
    }

    /**
     * @param      $cid
     * @param      $facility
     * @param      $role
     *
     * @return bool
     */
    public static function hasRole($cid, $facility, $role)
    {
        if (static::isVATUSAStaff() && $facility == "ZHQ") {
            return true;
        } elseif ($facility == "ZHQ" && $role != "ACE") {
            return false;
        }

        if (Schema::hasColumn('facilities', strtolower($role))) {
            $c = Facility::where(strtolower($role), $cid)->where('id', $facility)->count();
            if ($c) {
                return true;
            }
        }

        $c = Role::where('role', $role)->where('cid', $cid)->where('facility', $facility)->count();
        if ($c) {
            return true;
        }

        return false;
    }

    /**
     * @param null|integer $cid
     *
     * @return bool
     */
    public static function isVATUSAStaff($cid = null, $isApi = false, $skipWebTeam = false)
    {
        if (!\Auth::check() && !$isApi) {
            return false;
        }
        if ($cid == null || $cid == 0) {
            $cid = \Auth::user()->cid;
        }

        $user = User::where('cid', $cid)->first();
        if ($user == null) {
            return false;
        }

        if ($user->facility == "ZHQ") {
            return true;
        }

        if (!$skipWebTeam) {
            if (Role::where("facility", "ZHQ")->where("cid", $cid)->where("role", "LIKE", "US%")->count() >= 1) {
                return true;
            }
        } else {
            if (Role::where('facility', 'ZHQ')->where("cid", $cid)->where("role", "LIKE", "US%")->where("role",
                    "NOT LIKE", "USWT")->count() >= 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param null|integer $cid
     *
     * @return bool
     */
    public static function isWebTeam($cid = null)
    {
        if ($cid == null || $cid == 0) {
            $cid = \Auth::user()->cid;
            $user = \Auth::user();
        } else {
            $user = User::where('cid', $cid)->first();
        }
        if (!$user) {
            return false;
        }
        if (Role::where("facility", "ZHQ")->where("cid", $cid)->where("role", "USWT")->count() >= 1) {
            return true;
        }

        return false;
    }

    public static function isFacilityStaff($cid = null, $facility = null, $isApi = false)
    {
        if (!\Auth::check() && !$isApi) {
            return false;
        }
        if ($cid == null || $cid == 0) {
            $cid = \Auth::user()->cid;
        }
        if ($facility == null) {
            $facility = \Auth::user()->facility;
        }

        if (static::isVATUSAStaff($cid)) {
            return true;
        }

        if (static::isFacilitySeniorStaff($cid, $facility)) {
            return true;
        }

        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "WM")->count()) {
            return true;
        }
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "EC")->count()) {
            return true;
        }
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "FE")->count()) {
            return true;
        }

        if (Facility::where("wm", $cid)->where("id", $facility)->count()) {
            return true;
        }
        if (Facility::where("ec", $cid)->where("id", $facility)->count()) {
            return true;
        }
        if (Facility::where("fe", $cid)->where("id", $facility)->count()) {
            return true;
        }

        return false;
    }

    public static function isFacilitySeniorStaff($cid = null, $facility = null, $isApi = false)
    {
        if (!\Auth::check() && !$isApi) {
            return false;
        }
        if (($cid == null || $cid == 0)) {
            $cid = Auth::user()->cid;
        }
        if ($facility == null) {
            $facility = \Auth::user()->facility;
        }

        if (static::isVATUSAStaff($cid)) {
            return true;
        }

        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "ATM")->count()) {
            return true;
        }
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "DATM")->count()) {
            return true;
        }
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "TA")->count()) {
            return true;
        }

        if (Facility::where("atm", $cid)->where("id", $facility)->count()) {
            return true;
        }
        if (Facility::where("datm", $cid)->where("id", $facility)->count()) {
            return true;
        }
        if (Facility::where("ta", $cid)->where("id", $facility)->count()) {
            return true;
        }

        return false;
    }

    public static function isFacilitySeniorStaffExceptTA($cid = null, $facility = null, $isApi = false)
    {
        if (!\Auth::check() && !$isApi) {
            return false;
        }
        if (($cid == null || $cid == 0)) {
            $cid = Auth::user()->cid;
        }
        if ($facility == null) {
            $facility = \Auth::user()->facility;
        }

        if (static::isVATUSAStaff($cid)) {
            return true;
        }

        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "ATM")->count()) {
            return true;
        }
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "DATM")->count()) {
            return true;
        }

        if (Facility::where("atm", $cid)->where("id", $facility)->count()) {
            return true;
        }
        if (Facility::where("datm", $cid)->where("id", $facility)->count()) {
            return true;
        }

        return false;
    }

    public static function isTA($cid = null, $facility = null)
    {
        if (!Auth::check()) {
            return false;
        }
        if (($cid == null || $cid == 0)) {
            $cid = Auth::user()->cid;
        }
        if ($facility == null) {
            $facility = Auth::user()->facility;
        }

        if (static::isVATUSAStaff($cid)) {
            return true;
        }

        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "TA")->count()) {
            return true;
        }

        if (Facility::where("ta", $cid)->where("id", $facility)->count()) {
            return true;
        }

        return false;
    }

    public static function isAcademyStaff($cid = null)
    {
        if (!Auth::check()) {
            return false;
        }
        if (($cid == null || $cid == 0)) {
            $cid = Auth::user()->cid;
        }

        if (static::isVATUSAStaff($cid)) {
            return true;
        }

        if (Role::where("facility", "ZAE")->where('cid', $cid)->where("role", "STAFF")->count()) {
            return true;
        }

        return false;
    }

    /**
     * @param null|integer $cid
     * @param null|string  $facility
     *
     * @param bool         $includeVATUSAStaff
     *
     * @return bool
     */
    public static function isInstructor($cid = null, $facility = null, $includeVATUSAStaff = true)
    {
        if (!Auth::check()) {
            return false;
        }
        if (is_null($cid) || !$cid) {
            $cid = Auth::user()->cid;
            $user = Auth::user();
        }
        else {
            $user = User::find($cid);
        }
        if ($facility == null) {
            $facility = $user->facility;
        }

        // Check home controller, if no always assume no
        if (!$user->flag_homecontroller) {
            return false;
        }

        // First check facility and rating (excluding SUP)
        if ($user->facility == $facility && $user->rating >= Helper::ratingIntFromShort("I1") && $user->rating < Helper::ratingIntFromShort("SUP")) {
            return true;
        }

        //ADMs have INS Access
        if ($user->rating == Helper::ratingIntFromShort("ADM")) {
            return true;
        }

        // Check for an instructor role
        if (Role::where("facility", $facility)->where("cid", $cid)->where("role", "INS")->count()) {
            return true;
        }

        // Check for VATUSA staff, global access.
        if ($includeVATUSAStaff && RoleHelper::isVATUSAStaff($cid)) {
            return true;
        }

        return false;
    }

    public static function isMentor($cid = null)
    {
        if (!Auth::check()) {
            return false;
        }
        if ($cid == null || $cid == 0) {
            $cid = Auth::user()->cid;
        }
        $user = User::find($cid);
        if (!$user->flag_homecontroller) {
            return false;
        }
        if (!$user->facility()->active && $user->facility != "ZHQ") {
            return false;
        }

        if (Role::where("cid", $cid)->where("facility", $user->facility)->where("role", "MTR")->count()) {
            return true;
        }

        return false;
    }

    public static function getStaff($facility = null, $getVATUSA = true)
    {
        if (!$facility) {
            $facility = \Auth::user()->facility;
        }

        $staff = [];
        $f = Facility::find($facility);
        if ($f->atm) {
            $staff[] = ['cid' => $f->atm, 'name' => $f->atm()->fullname(), 'role' => "ATM"];
        }
        if ($f->datm) {
            $staff[] = ['cid' => $f->datm, 'name' => $f->datm()->fullname(), 'role' => "DATM"];
        }
        if ($f->ta) {
            $staff[] = ['cid' => $f->ta, 'name' => $f->ta()->fullname(), 'role' => "TA"];
        }
        if ($f->ec) {
            $staff[] = ['cid' => $f->ec, 'name' => $f->ec()->fullname(), 'role' => "EC"];
        }
        if ($f->fe) {
            $staff[] = ['cid' => $f->fe, 'name' => $f->fe()->fullname(), 'role' => "FE"];
        }
        if ($f->wm) {
            $staff[] = ['cid' => $f->wm, 'name' => $f->wm()->fullname(), 'role' => "WM"];
        }

        if ($facility != "ZAE") {
            // Eloquent: I1s/I2s/I3s Listing (do not include SUPs/ADMs)
            foreach (\App\User::where('rating', '>=', \App\Classes\Helper::ratingIntFromShort('I1'))
                ->where('rating', '!=', \App\Classes\Helper::ratingIntFromShort('SUP'))
                ->where('rating', '!=', \App\Classes\Helper::ratingIntFromShort('ADM'))
                ->where('facility', $facility)
                ->orderBy('fname')
                ->orderBy('lname')
                ->get() as $user) {
                    if (!static::isFacilityStaff($user->cid, $facility)) {
                        $staff[] = [
                            'cid' => $user->cid,
                            'name' => $user->fullname(),
                            'role' => 'INS'
                        ];
                    }
            }

            // Eloquent: SUPs Tagged as Instructors
            foreach (\App\Role::where('facility', $facility)->where('role', 'INS')->get() as $s) {
                if (!static::isFacilityStaff($s->cid, $facility)) {
                    $staff[] = [
                        'cid' => $s->cid,
                        'name' => $s->user->fullname(),
                        'role' => 'INS'
                    ];
                }
            }
        }

        if ($getVATUSA && $facility == "ZHQ") {
            // Eloquent: All VATUSA Staff
            foreach (\App\Role::where('facility', 'ZHQ')
                ->where('role', 'LIKE', "US%")
                ->orderBy("role")
                ->get() as $r) {
                    $staff[] = [
                        'cid'  => $r->cid,
                        'name' => $r->user->fullname() . " (" . static::roleTitle($r->role) . ")",
                        'role' => str_replace("US", "VATUSA", $r->role)
                    ];
            }
        }

        if ($facility == "ZAE") {
            // Eloquent: VATUSA Training Staff (%3 [e.g. 3/13])
            foreach(\App\Role::where('facility', 'ZHQ')
                ->where('role', 'LIKE', "%3")
                ->orderBy("role")
                ->get() as $v) {
                    $staff[] = [
                        'cid' => $v->cid,
                        'name' => $v->user->fullname() . " (" . static::roleTitle($v->role) . ")",
                        'role' => str_replace("US", "VATUSA", $v->role)
                    ];
                }
        }

        return $staff;
    }

    public static function deleteStaff($facility, $cid, $pos)
    {
        $id = intval($pos);
        switch ($id) {
            case 1:
                $pos = 'ATM';
                break;
            case 2:
                $pos = 'DATM';
                break;
            case 3:
                $pos = 'TA';
                break;
            case 4:
                $pos = 'EC';
                break;
            case 5:
                $pos = 'FE';
                break;
            case 6:
                $pos = 'WM';
                break;
        }
        $spos = strtolower($pos);

        $fu = Facility::where('id', $facility)->first();
        $oldstaff = $fu->$spos;

        if ($oldstaff == $cid) {
            $fu->$spos = 0;
            $fu->save();
            $user = User::where('cid', $oldstaff)->first();
            $log = new Actions();
            $log->to = $user->cid;
            $log->log = "User removed from " . $fu->name . " $pos by " . \Auth::user()->fullname() . ".";
            $log->save();

            $email = $fu->id . "-" . $spos . "@vatusa.net";
            if (!EmailHelper::isStaticForward($email)) {
                if ($spos == "atm") {
                    $fwd = "vatusa" . $fu->region . "@vatusa.net";
                } else {
                    $fwd[] = $fu->id . "-atm@vatusa.net";
                    if ($spos != "datm") {
                        $fwd[] = $fu->id . "-datm@vatusa.net";
                    }
                }

                EmailHelper::setForward($email, $fwd);
            }

            SMFHelper::setPermissions($user->cid);
        }
    }

    public static function validRoles()
    {
        return ['MENTOR', 'INS'];
    }
}
