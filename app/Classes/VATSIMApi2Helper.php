<?php

namespace App\Classes;
use App\Models\User;
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
        $key = VATSIMApi2Helper::_key();
        if ($key === null) {
            return false;
        }
        $data = [
            "id" => $cid,
            "rating" => $rating,
            "comment" => "VATUSA Rating Change Integration"
        ];
        $json = json_encode($data);
        $client = new Client(['headers' => ['Authorization' => "Token {$key}"]]);
        $response = $client->patch($fullURL, ['body' => $json]);
        return $response->getStatusCode() == 200;
    }

    static function syncCID (int $cid): bool {
        $path = "/members/{$cid}";
        $fullURL = VATSIMApi2Helper::_url() . $path;
        $key = VATSIMApi2Helper::_key();
        if ($key === null) {
            return false;
        }
        $client = new Client(['headers' => ['Authorization' => "Token {$key}"]]);
        $response = $client->get($fullURL);
        $data = json_decode($response->getBody(), true);
        $user = User::find($cid);
        if (!$user) {
            return false;
        }

        $user->fname = $data['name_first'];
        $user->lname = $data['name_last'];
        $user->email = $data['email'];
        $user->rating = $data['rating'];
        $user->flag_homecontroller = $data['division_id'] == 'USA';
        $user->save();
        if ($user->rating <= 0) {
            if ($user->flag_homecontroller) {
                if ($user->facility != "ZAE") {
                    $user->removeFromFacility("Automated", "Suspended/Inactive", "ZAE");
                }
            } else if ($user->facility != "ZZN") {
                $user->removeFromFacility("Automated", "Suspended/Inactive", "ZZN");
            }
            $user->removeFromVisitingFacilities("Suspended/Inactive");
        }

        return true;
    }
}