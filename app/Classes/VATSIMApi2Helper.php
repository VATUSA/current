<?php

namespace App\Classes;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use Illuminate\Support\Str;

class VATSIMApi2Helper {

    private static function _url() {
        return env('VATSIM_API2_URL');
    }

    private static function _key() {
        return env('VATSIM_API2_KEY', null);
    }

    private static function _client(): Client {
        $key = VATSIMApi2Helper::_key();
        return new Client(['base_uri' => self::_url(),'headers' => ['Authorization' => "Token {$key}"]]);
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

    static function fetchOrgMemberPage($page) {
        $limit = 100;
        $offset = $limit * $page;
        $path = "/v2/orgs/division/USA?limit={$limit}&offset={$offset}";
        $client = self::_client();
        try {
            $response = $client->get($path);
        } catch (Exception\GuzzleException $e) {
            echo $e->getMessage() . "\n";
            return null;
        }
        return json_decode($response->getBody(), true);
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
            if ($e->getResponse()->getStatusCode() == 404) {
                $user = User::find($cid);
                if (!$user) {
                    return false;
                }
                $user->rating = -1;
                $user->flag_homecontroller = 0;
                $user->save();
                $user->removeFromFacility("Automated", "Inactive", "ZZI");
            }
            echo $e->getMessage(). "\n";
            return false;
        }
        $data = json_decode($response->getBody(), true);
        self::processMemberData($data);
        return true;
    }

    static function processMemberData($data) {
        $user = User::find($data['id']);
        if (!$user) {
            // TODO: Create User
            return false;
        }
        if (array_key_exists('name_first', $data) && array_key_exists('name_last', $data)) {
            $user->fname = $data['name_first'];
            $user->lname = $data['name_last'];
        }
        if (array_key_exists('email', $data)) {
            $user->email = $data['email'];
        }
        if ($data['division_id'] == 'USA' && $user->rating == 0 and $data['rating'] > 0) {
            EmailHelper::sendEmail(
                ["vatusa2@vatusa.net"],
                "Suspension Expired",
                "emails.user.suspension_expired",
                [
                    'cid' => $user->cid,
                    'name' => $user->fname . " " . $user->lname,
                ]
            );
        }
        $user->rating = $data['rating'];
        $user->flag_homecontroller = $data['division_id'] == 'USA';
        $user->last_cert_sync = Carbon::now();
        $user->save();
        if ($user->rating == -1) {
            $user->removeFromFacility("Automated", "Inactive", "ZZI");
            $user->removeFromVisitingFacilities("Suspended");
        } else if ($user->rating == 0) {
            if ($user->flag_homecontroller) {
                if ($user->facility != "ZAE") {
                    $user->removeFromFacility("Automated", "Suspended", "ZAE");
                }
            } else if ($user->facility != "ZZN") {
                $user->removeFromFacility("Automated", "Suspended", "ZZN");
            }
            $user->removeFromVisitingFacilities("Suspended");
        } else if (!$user->flag_homecontroller && $user->facility != 'ZZN') {
            $user->removeFromFacility("Automated", "Left Division", "ZZN");
        } else if ($user->facility == "ZZI" && $user->flag_homecontroller) {
            $user->flag_needbasic = 1;
            $user->save();
            TransferHelper::forceTransfer($user, "ZAE", "Returned from Inactivity");
        } else if ($user->facility == "ZZN" && $user->flag_homecontroller) {
            $user->flag_needbasic = 1;
            $user->save();
            TransferHelper::forceTransfer($user, "ZAE", "Joined division");
        } else if ($user->facility == "ZZI" && !$user->flag_homecontroller) {
            $user->removeFromFacility("Automated", "Returned from Inactivity", "ZZN");
        }
    }
}