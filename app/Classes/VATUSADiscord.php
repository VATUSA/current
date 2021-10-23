<?php
/**
 * Interact with Notifications
 * @author Blake Nahin <b.nahin@vatusa.net>
 */

namespace App\Classes;

use App\Models\Facility;
use App\Models\FacilityNotificationChannel;
use App\Models\NotificationSetting;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface;

class VATUSADiscord
{
    private $guzzle;

    public const NOTIFY_EMAIL = 1;
    public const NOTIFY_DISCORD = 2;
    public const NOTIFY_BOTH = 3;

    /**
     * VATUSA Discord constructor.
     */
    public function __construct()
    {
        $this->guzzle = new Client(['base_uri' => config('services.discord.botServer')]);
    }

    /**
     * Get the user's Notification option for a type.
     *
     * @param User   $user
     * @param string $type
     *
     * @return int
     */
    public function getNotificationOption(User $user, string $type): int
    {
        $record = NotificationSetting::where('cid', $user->cid)->where('type', $type)->first();

        return $record ? $record->option : 0;
    }

    /**
     * Get the facility's Notification channel for a type.
     *
     * @param Facility $facility
     * @param string   $type
     *
     * @return int
     */
    public function getFacilityNotificationChannel(Facility $facility, string $type): int
    {
        $record = FacilityNotificationChannel::where('facility', $facility->id)->where('type', $type)->first();

        return $record && $facility->discord_guild ? $record->channel : 0;
    }

    /**
     * Get an array of all the user's notification options.
     *
     * @param User $user
     *
     * @return array
     */
    public function getAllUserNotificationOptions(User $user): array
    {
        $records = NotificationSetting::where('cid', $user->cid)->get();
        $return = array();
        foreach ($records as $record) {
            $return[$record->type] = $record->option;
        }

        return $return;
    }

    /**
     * Get an array of all the facility's notification channels.
     * @param Facility $facility
     *
     * @return array
     */
    public function getAllFacilityNotificationChannels(Facility $facility): array
    {
        $records = FacilityNotificationChannel::where('facility', $facility->id)->get();
        $return = array();
        foreach ($records as $record) {
            $return[$record->type] = $record->channel;
        }

        return $return;
    }

    /**
     * Send Notification to Bot Server
     *
     * @param string      $type      The notification identifier.
     * @param string      $medium    The medium of notification, dm | discord.
     * @param array       $data      The notification data.
     * @param string|null $guildId   The guild's ID.
     * @param string|null $channelId The channel's ID.
     * @param string|null $id        The user's ID.
     *
     * @return bool
     */
    public function sendNotification(
        string $type,
        string $medium,
        array $data,
        ?string $guildId = null,
        ?string $channelId = null,
        ?string $id = null
    ): bool {
        if ($guildId && $channelId) {
            $data = array_merge($data, compact('guildId', 'channelId'));
        }
        try {
            $this->sendRequest('POST', "notifications/$medium/$type" . ($id ? "/$id" : ""), ['json' => $data]);
        } catch (Exception $e) {
            return 0;
        }

        return 1;
    }

    /**
     * Determine if the User has configured the Notification.
     *
     * @param \App\Models\User $user
     * @param string           $type
     * @param string           $medium
     *
     * @return bool
     */
    public function userWantsNotification(User $user, string $type, string $medium): bool
    {
        if (!$user->discord_id) {
            return false;
        }
        $option = $this->getNotificationOption($user, $type);
        if ($option === self::NOTIFY_BOTH) {
            return true;
        }

        switch (strtolower($medium)) {
            case "discord":
                return $option === self::NOTIFY_DISCORD;
            case "email":
                return $option === self::NOTIFY_EMAIL;
            default:
                return false;
        }
    }

    /**
     * Get an array of all the Guilds that the User is an admin in
     * and that the Bot is a member of.
     *
     * @param \App\Models\User $user
     *
     * @return array
     */
    public function getUserAdminGuilds(User $user): array
    {
        try {
            $response = $this->sendRequest("GET", "/guilds/" . $user->discord_id);
        } catch (Exception $e) {
            return [];
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * Get an array of all the channels in a Guild.
     * @param string $guild
     *
     * @return array
     */
    public function getGuildChannels(string $guild): array
    {
        try {
            $response = $this->sendRequest("GET", "/guild/$guild/channels");
        } catch (Exception $e) {
            return [];
        }
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * Send request to the Bot Server.
     *
     * @param string     $method The request method.
     * @param string     $uri    The request URI.
     * @param array|null $data   The request body.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    private function sendRequest(string $method, string $uri, ?array $data = null): ResponseInterface
    {
        $iss = Carbon::now();
        $jwt = JWT::encode([
            'iat' => $iss->getTimestamp(),
            'iss' => config('app.url'),
            'aud' => config('services.discord.botServer'),
            'nbf' => $iss->getTimestamp(),
            'exp' => $iss->addMinute()->getTimestamp()
        ], config('services.discord.botSecret'), 'HS512');
        try {
            return $this->guzzle->request($method, $uri,
                ['json' => $data ?? [], 'headers' => ['Authorization' => 'Bearer ' . $jwt]]);
        } catch (GuzzleException $e) {
            throw new Exception("Unable to make request to the Discord Bot Server. " . $e->getMessage());
        }
    }
}