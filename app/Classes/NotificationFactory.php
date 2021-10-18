<?php
/**
 * Interact with Notifications
 * @author Blake Nahin <b.nahin@vatusa.net>
 */

namespace App\Classes;

use App\Models\NotificationSetting;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NotificationFactory
{
    private $guzzle;

    public const NOTIFY_EMAIL = 1;
    public const NOTIFY_DISCORD = 2;
    public const NOTIFY_BOTH = 3;
    public const NOTFIY_NONE = 0;

    public function __construct()
    {
        $this->guzzle = new Client(['base_uri' => config('services.discord.botserver')]);
    }

    public function getOption(User $user, string $type): int
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

    /**
     * Send Notification to Bot Server
     */
    public function sendNotification(string $type, array $data, ?string $id = null): bool
    {
        try {
            $r = $this->guzzle->request('POST', "notifications/" . $type . ($id ? "/$id" : ''), ['json' => $data]);
        } catch (GuzzleException $e) {
            return 0;
        }

        return 1;
    }

    public function wantsNotification(User $user, string $type, string $medium)
    {
        $option = $this->getOption($user, $type);
        if ($option === self::NOTIFY_BOTH) {
            return true;
        }

        switch (strtolower($medium)) {
            case "discord":
                return $option === self::NOTIFY_DISCORD;
                break;
            case "email":
                return $option === self::NOTIFY_EMAIL;
                break;
            default:
                return false;
        }
    }
}