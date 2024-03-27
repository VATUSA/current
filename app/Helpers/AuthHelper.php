<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    public static $aclFlagsMap = [];
    private static function aclFlags(int $cid): ACLFlags {
        if (array_key_exists($cid, self::$aclFlagsMap)) {
            return self::$aclFlagsMap[$cid];
        }
        $user = User::where('cid', $cid)->with('roles')->first();
        $flags = new ACLFlags($user);
        self::$aclFlagsMap[$user->cid] = $flags;
        return $flags;
    }

    public static function authACL(): ACLFlags {
        if (!Auth::check()) {
            return new ACLFlags();
        }
        $user = Auth::user();
        return self::aclFlags($user->cid);
    }

    public static function cidACL($cid): ACLFlags {
        return self::aclFlags($cid);
    }
}