<?php

namespace App\Classes;

use App\Models\User;

class DiscordHelper
{
    public static function assignRoles($cid) {
        $user = User::where('cid', $cid)->first();
        if ($user->discord_id != null)
            file_get_contents("https://bot.vatusa.net/assignRoles/" . $user->discord_id);
    }
}