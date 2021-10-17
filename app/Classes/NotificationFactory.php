<?php
/**
 * Interact with Notifications
 * @author Blake Nahin <b.nahin@vatusa.net>
 */

namespace App\Classes;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationFactory
{
    public static function getOption(User $user, string $type): int
    {
        $record = NotificationSetting::where('cid', $user->cid)->where('type', $type)->first();

        return $record ? $record->option : 0;
    }

    public function getAllUserOptions(User $user): array
    {
        $records = NotificationSetting::where('cid', $user->cid)->get();
        $return = array();
        foreach ($records as $record) {
            $return[$record->type] = $record->option;
        }

        return $return;
    }
}