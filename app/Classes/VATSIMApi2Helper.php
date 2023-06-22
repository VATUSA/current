<?php

namespace App\Classes;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;

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
        try {
            $response = $client->get($fullURL);
        } catch (Exception\GuzzleException $e) {
            echo $e->getMessage();
            return false;
        }
        $data = json_decode($response->getBody(), true);
        $user = User::find($cid);
        if (!$user) {
            return false;
        }
        if (array_key_exists('name_first', $data) && array_key_exists('name_last', $data)) {
            $user->fname = $data['name_first'];
            $user->lname = $data['name_last'];
        }
        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }
        $user->rating = $data['rating'];
        $user->flag_homecontroller = $data['division_id'] == 'USA';
        $user->last_cert_sync = Carbon::now();
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