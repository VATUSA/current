<?php

namespace App\Classes;
use GuzzleHttp\Client;

class VATSIMApi2Helper {

    private static function _url() {
        return env('VATSIM_API2_URL');
    }

    private static function _key() {
        return env('VATSIM_API2_KEY', null);
    }
    static function updateRating(int $cid, int $rating): bool {
        $path = "/members/{$cid}";
        $fullURL = VATSIMApi2Helper::_url() . $path;
        if (VATSIMApi2Helper::_key() === null) {
            return false;
        }
        $data = [
            "id" => $cid,
            "rating" => $rating,
        ];
        $json = json_encode($data);
        $client = new Client();
        $response = $client->patch($fullURL, ['body' => $json]);
        return $response->getStatusCode() == 200;
    }
}