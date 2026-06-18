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