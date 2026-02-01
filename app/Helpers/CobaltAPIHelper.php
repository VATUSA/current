<?php

namespace App\Helpers;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use GuzzleHttp\RequestOptions;

class CobaltAPIHelper
{
    private static function _client() {
        $client = new Client([
            'base_uri' => config('cobalt.url'),
            'headers' => [
                'X-Auth-Token' => config('cobalt.token'),
                'User-Agent' => 'VATUSA/current +https://vatusa.net',
                'Content-Type' => 'application/json',
            ]
        ]);

        return $client;
    }

    public static function getCobaltUserToken($cid): ?string {
        $path = "/api/token/{$cid}";
        $client = self::_client();
        try {
            $response = $client->get($path);
        } catch (Exception\GuzzleException $e) {
            return null;
        }
        $data = json_decode($response->getBody(), true);
        return $data['token'] ?? null;
    }

    public static function syncRolesForUser(User $user) {
        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = ['role' => $role->role, 'facility' => $role->facility];
        }
        self::postSyncRoles($user->cid, $roles);
    }

    public static function postSyncRoles($cid, $roles): bool {
        $path = "/api/roles/legacy_sync";
        $client = self::_client();
        $data = [
            'cid' => $cid,
            'roles' => $roles,
        ];
        $json = json_encode($data);
        try {
            $response = $client->post($path, [RequestOptions::BODY => $json]);
        } catch (Exception\GuzzleException $e) {
            return false;
        }
        return $response->getStatusCode() == 200;
    }

    public static function getNewsPost($id) {
        $path = "/api/news/{$id}";
        $client = self::_client();
        try {
            $response = $client->get($path);
        } catch (Exception\GuzzleException $e) {
            return null;
        }
        return json_decode($response->getBody(), true);
    }
}