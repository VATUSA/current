<?php

namespace App\Cobalt;

class CobaltSession
{
    public CobaltUser $user;
    public array $global_permissions;
    public array $facility_permissions;

    function __construct(array $json) {
        $this->user = new CobaltUser($json['user']);
    }

    /**
     * Extract the CID from a cobalt JWT.
     *
     * Fast path: decode the JWT locally with the shared key (no network call).
     * Fallback: call cobalt /tokenSession if local decode fails (key mismatch, etc.).
     */
    public static function getCidFromToken(string $token): ?int
    {
        $jwtKey = config('cobalt.jwt_key', '');

        if (!empty($jwtKey) && class_exists(\Firebase\JWT\JWT::class)) {
            try {
                $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtKey, 'HS256'));
                $cid = (int) ($decoded->cid ?? 0);
                if ($cid > 0) {
                    return $cid;
                }
            } catch (\Throwable $e) {
                // Fall through to /tokenSession
            }
        }

        $json = CobaltAPIHelper::getUserSessionFromToken($token);
        if ($json === null) {
            return null;
        }
        $cid = (int) ($json['user']['cid'] ?? 0);
        return $cid > 0 ? $cid : null;
    }

    public static function fetchFromToken($token): ?CobaltSession {
        $json = CobaltAPIHelper::getUserSessionFromToken($token);
        if ($json === null) {
            return null;
        }
        return new CobaltSession($json);
    }
}

class GlobalPermission {
    public string $action;
    public string $object;
}

class FacilityPermission {
    public string $action;
    public string $object;
    public string $facility;
}
