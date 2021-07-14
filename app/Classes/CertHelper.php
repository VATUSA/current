<?php namespace App\Classes;

use App\Models\Actions;
use App\Models\User;
use Auth;

class CertHelper
{
    private static $baseUrl = "http://cert.vatsim.net/vatsimnet/admin/";
    private static $authId;
    private static $authPassword;

    // Download Database txt file from VATSIM Cert
    // Returns array of lines for processing
    public static function downloadDivision()
    {
        if (env('CERT_ID') == 0) {
            return;
        }
        $url = static::buildUrl("divdb.php");
        $result = file_get_contents($url);
        $lines = array();
        $lines = explode("\n", $result);

        return $lines;
    }

    // Change a user's rating
    // VATSIM requests integer style ratings
    // newRating can be integer or short.
    public static function changeRating($cid, $newRating, $addToDatabase = false)
    {
        if (env('APP_ENV', 'dev') != "prod") {
            if ($addToDatabase) {
                $user = User::find($cid);
                $user->rating = $newRating;
                $user->save();

                $action = new Actions();
                $action->to = $cid;
                $action->log = "Rating set to " . Helper::ratingShortFromInt($newRating) . " by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ")";
                $action->save();
            }

            # Don't do anything
            return 1;
        }

        if (env('CERT_ID') == 0) {
            return 1;
        }

        if (!is_numeric($newRating)) {
            $newRating = Helper::ratingIntFromShort($newRating);
        }
        $url = static::buildUrl("ratch.php", array('id' => $cid, 'rat' => $newRating));
        $result = file_get_contents($url);
        if ($result == "ERR:NoExist" || $result == "ERR:NotAllowed") {
            return 0;
        }

        if ($addToDatabase) {
            $user = User::find($cid);
            $user->rating = $newRating;
            $user->save();

            $action = new Actions();
            $action->to = $cid;
            $action->log = "Rating set to " . Helper::ratingShortFromInt($newRating) . " by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ")";
            $action->save();
        }

        return 1;
    }

    private static function buildUrl($url, $args = array())
    {
        $queryString = "";
        foreach ($args as $key => $value) {
            if ($queryString != "") {
                $queryString .= "&";
            }
            $queryString .= "$key=$value";
        }
        if ($queryString) {
            $queryString .= "&";
        }
        $queryString .= "authid=" . env('CERT_ID') . "&authpassword=" . env('CERT_PASSWORD');

        return static::$baseUrl . $url . "?" . $queryString;
    }
}