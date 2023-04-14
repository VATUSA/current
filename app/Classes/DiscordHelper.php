<?php

namespace App\Classes;

use App\Models\User;
use GuzzleHttp\Client as Guzzle;

class DiscordHelper
{
    public static function assignRoles($cid) {
        $guzzle = new Guzzle();
        $user = User::where('cid', $cid)->first();
        if ($user->discord_id != null) {
            $return = $guzzle->post("https://bot.vatusa.net/assignRoles/" . $user->discord_id);
            return $return->getBody();
        }
    }
}