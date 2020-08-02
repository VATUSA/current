<?php

namespace App\Classes;

use App\User;
use App\Facility;

class Helper
{
    public static function version()
    {
        $version = "VATUSA-";
        if (file_exists(base_path("gitversion"))) {
            $version .= file_get_contents(base_path('gitversion'));
        } else {
            $version .= "dev";
        }

        return $version;
    }

    public static function nameFromCID($cid, $retCID = 0)
    {
        $ud = User::where('cid', $cid)->count();
        if ($ud) {
            $u = User::where('cid', $cid)->first();

            return ($retCID ? $u->fname . ' ' . $u->lname . ' - ' . $u->cid : $u->fname . ' ' . $u->lname);
        } elseif ($cid == "0") {
            return "Automated";
        } else {
            return 'Unknown';
        }
    }

    public static function emailFromCID($cid)
    {
        $u = User::where('cid', $cid)->count();
        if ($u) {
            $u = User::where('cid', $cid)->first();

            return $u->email;
        } elseif ($cid == "0") {
            return "Automated";
        } else {
            return 'Unknown';
        }
    }

    public static function facFromCID($cid)
    {
        $u = User::find($cid);

        return $u ? $u->facility : "Unknown";
    }

    public static function ratingIntFromShort($short)
    {
        return \App\Rating::where('short', $short)->first()->id;
    }

    public static function ratingShortFromInt($rating)
    {
        return \App\Rating::find($rating)->short;
    }

    public static function ratingLongFromInt($rating)
    {
        $rating = \App\Rating::find($rating);

        return $rating->long;
    }

    public static function ratingLngSht($rat)
    {
        return \App\Rating::where('long', $rat)->first()->short;
    }

    public static function ratingShtLng($rat)
    {
        return \App\Rating::where('short', $rat)->first()->long;
    }

    public static function facShtLng($fac)
    {
        $facility = Facility::find($fac);

        return ($facility != null ? $facility->name : 'Unknown');
    }
}