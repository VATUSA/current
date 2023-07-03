<?php

namespace App\Classes;

use App\Models\User;
use App\Models\Facility;

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

    public static function apiUrl()
    {
        if (env('APP_ENV') == "dev") {
            return env('API_URL');
        }
        return str_replace('www.vatusa', 'api.vatusa', config('app.url'));
    }

    public static function mainUrl()
    {
       return config('app.url');
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
        return \App\Models\Rating::where('short', $short)->first()->id;
    }

    public static function ratingShortFromInt($rating)
    {
        return \App\Models\Rating::find($rating)->short;
    }

    public static function ratingLongFromInt($rating)
    {
        $rating = \App\Models\Rating::find($rating);

        return $rating->long;
    }

    public static function ratingLngSht($rat)
    {
        return \App\Models\Rating::where('long', $rat)->first()->short;
    }

    public static function ratingShtLng($rat)
    {
        return \App\Models\Rating::where('short', $rat)->first()->long;
    }

    public static function facShtLng($fac)
    {
        $facility = Facility::find($fac);

        return ($facility != null ? $facility->name : 'Unknown');
    }

    public static function numToOrdinalWord($num)
    {
        $first_word = array(
            'Zeroeth',
            'First',
            'Second',
            'Third',
            'Fouth',
            'Fifth',
            'Sixth',
            'Seventh',
            'Eighth',
            'Ninth',
            'Tenth',
            'Elevents',
            'Twelfth',
            'Thirteenth',
            'Fourteenth',
            'Fifteenth',
            'Sixteenth',
            'Seventeenth',
            'Eighteenth',
            'Nineteenth',
            'Twentieth'
        );
        $second_word = array('', '', 'Twenty', 'Thirty', 'Forty', 'Fifty');

        if ($num <= 20) {
            return $first_word[$num];
        }

        $first_num = substr($num, -1, 1);
        $second_num = substr($num, -2, 1);

        return $string = str_replace('y-eth', 'ieth', $second_word[$second_num] . '-' . $first_word[$first_num]);
    }
}