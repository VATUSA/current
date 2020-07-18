<?php

namespace App\Classes;

use App\User;

class SMFHelper
{
    public static function setGroups($cid, $grp, $addl = "")
    {
        \DB::connection('forum')->table("smf_members")->where('member_name', $cid)
            ->update([
                'id_group'          => $grp,
                'additional_groups' => $addl
            ]);
    }

    public static function findFacilityStaff($facility)
    {
        return static::findGroup($facility . " Staff");
    }

    public static function findGroup($group)
    {
        $staff = 0;
        $grp = \DB::connection('forum')->table("smf_membergroups")->where('group_name', $group)->first();
        if ($grp) {
            $staff = $grp->id_group;
        }

        return $staff;
    }

    public static function setPermissions($cid)
    {
        $role = "";
        $addl = "";
        $grp = "";

        $user = User::find($cid);

        if ($user->rating == Helper::ratingIntFromShort("ADM")) {
            if (!RoleHelper::isVATUSAStaff()) {
                static::setGroups($cid, static::findGroup("VATSIM Leadership"));

                return;
            } else {
                // Allow for them to get the VATUSA Staff group
                // as secondary group if they have a VATUSA Staff role
                // per Mark Hubbert
                static::setGroups($cid, static::findGroup("VATSIM Leadership"), static::findGroup("VATUSA Staff"));

                return;
            }
        }

        if ($user->facility()->atm == $user->cid || $user->facility()->datm == $user->cid) {
            $role = "ATM";
        } elseif ($user->facility()->ta == $user->cid) {
            $role = "TA";
        } elseif ($user->facility()->ec == $user->cid) {
            $role = "EC";
        } elseif ($user->facility()->fe == $user->cid) {
            $role = "FE";
        } elseif ($user->facility()->wm == $user->cid) {
            $role = "WM";
        }

        if ($role) {
            $grp = static::findFacilityStaff($user->facility);
        } else {
            $grp = static::findGroup("Members");
        }

        if (RoleHelper::isVATUSAStaff($cid, true, true)) {
            $grp = static::findGroup("VATUSA Staff");
            $role = "";
            if (in_array($user->getPrimaryRole(), ['1', '2', '6'])) {
                $role = "Administrator";
            }
        }
        if ($role) {
            $addl = static::findGroup($role);
        }

        if (RoleHelper::hasRole($cid, 'ZHQ', 'ACE')) {
            if ($addl) {
                $addl .= ",";
            }
            $addl .= static::findGroup("Ace Team");
        }
        if ($user->rating === Helper::ratingIntFromShort("SUP") && $grp === static::findGroup("Members")) {
            //Supervisor over Members (same perms set), WT, INSs, and MTRs
            if ($addl) {
                $addl .= ",";
            }
            $grp = static::findGroup("VATSIM Supervisors");
            $addl .= static::findGroup("Members");
        }
        if (RoleHelper::isWebTeam($cid)) {
            if ($addl) {
                $addl .= ",";
            }
            if ($grp === static::findGroup("Members")) {
                //WT Priority over INS, MTRs, Members
                $grp = static::findGroup("Web Team");
                $addl .= static::findGroup("Members");
            } else {
                $addl .= static::findGroup("Web Team");
            }
        }
        if (RoleHelper::hasRole($cid, $user->facility,
                "INS") || ($user->rating >= Helper::ratingIntFromShort("I1") && $user->rating < Helper::ratingIntFromShort("SUP"))) {
            if ($addl) {
                $addl .= ",";
            }
            if ($grp === static::findGroup("Members")) {
                //INS Priority over Members and MTRs
                $grp = static::findGroup("Instructors");
                $addl .= static::findGroup("Members");
            } else {
                $addl .= static::findGroup("Instructors");
            }
        }

        if (RoleHelper::hasRole($cid, $user->facility, "MTR")) {
            if ($addl) {
                $addl .= ",";
            }
            if ($grp === static::findGroup("Members")) {
                //MTR Priority over Members
                $grp = static::findGroup("Mentors");
                $addl .= static::findGroup("Members");
            } else {
                $addl .= static::findGroup("Mentors");
            }
        }


        static::setGroups($cid, $grp, $addl);
    }

    public static function createPost($memberID, $board, $subject, $body)
    {/*
        $smf_subject = $subject;
        $smf_subject = addslashes(htmlspecialchars($smf_subject));
        $smf_body = addslashes(htmlspecialchars($body));
        $smf_board = $board;
        $smf_member = $memberID; //Website psuedo user
        require_once(base_path() . "/../forums/SSI.php");
        require_once(base_path() . "/../forums/Sources/Load.php");
        require_once(base_path() . "/../forums/Sources/Subs.php");
        require_once(base_path() . "/../forums/Sources/Subs-Post.php");

        $msgOptions = [
            'postmod_active' => false,
            'subject' => $smf_subject,
            'body' => $smf_body
        ];
        $topicOptions = [
            'board' => $smf_board,
        ];
        $posterOptions = [
            'id' => $smf_member
        ];
        createPost($msgOptions, $topicOptions, $posterOptions);*/
    }
}
