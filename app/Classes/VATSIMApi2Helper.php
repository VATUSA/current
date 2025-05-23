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
        return new Client([
            'base_uri' => self::_url(),
            'headers' => [
                'Authorization' => "Token {$key}",
                'User-Agent' => 'VATUSA/current +https://vatusa.net',
            ],
        ]);
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

    static function fetchRatingHours($cid) {
        $path = "/v2/members/{$cid}/stats";
        $client = self::_client();
        try {
            $response = $client->get($path);
        } catch (Exception\GuzzleException $e) {
            return null;
        }
        return json_decode($response->getBody(), true);
    }

    static function fetchOrgMemberPage($page) {
        $limit = 2500;
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
           echo "VATSIM API Key not configured. Skipping sync for CID: {$cid}";
            return false;
        }
        $client = new Client(['headers' => ['Authorization' => "Token {$key}"]]);

        $maxRetries = 3; // Maximum number of retries for 429 errors
        $retryDelaySeconds = 60; // Delay in seconds before retrying

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $client->get($fullURL);
                // If successful, process data and exit the retry loop
                $data = json_decode($response->getBody(), true);
                self::processMemberData($data);
                return true;

            } catch (Exception\GuzzleException $e) {
                if ($e->hasResponse()) {
                    $statusCode = $e->getResponse()->getStatusCode();

                    if ($statusCode == 429) {
                        // Rate limit hit
                        echo "VATSIM API rate limit hit for CID: {$cid}. Attempt {$attempt}/{$maxRetries}. Waiting {$retryDelaySeconds} seconds...";
                        if ($attempt < $maxRetries) {
                            sleep($retryDelaySeconds);
                            continue;
                        } else {
                            echo "VATSIM API rate limit persisted after {$maxRetries} attempts for CID: {$cid}. Aborting sync for this user.";
                            return false;
                        }
                    } elseif ($statusCode == 404) {
                        // Handle 404 (User not found)
                        echo "VATSIM API returned 404 for CID: {$cid}. Marking as inactive.";
                        $user = User::find($cid);
                        if ($user) {
                            $user->rating = -1;
                            $user->flag_homecontroller = 0;
                            $user->save();
                            $user->removeFromFacility("Automated", "Inactive", "ZZI");
                        } else {
                            echo "User with CID {$cid} not found in local database during 404 handling.";
                        }
                        return false;
                    } else {
                        echo "VATSIM API request failed for CID: {$cid} with status code {$statusCode}: " . $e->getMessage();
                        return false;
                    }
                } else {
                    echo "VATSIM API request failed for CID: {$cid} with no response: " . $e->getMessage();
                    return false;
                }
            }
        }

        echo "Exited syncCID retry loop unexpectedly for CID: {$cid}.";
        return false;
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
            $user->removeFromVisitingFacilities("Inactive");
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