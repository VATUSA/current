<?php

namespace App\Cobalt;

class CobaltUser
{
    public int $cid;
    public CobaltNetworkUser $network_user;
    public CobaltDivisionUser $division_user;

    function __construct(array $json) {
        $this->cid = $json['cid'];
        $this->network_user = new CobaltNetworkUser($json['network_user']);
        $this->division_user = new CobaltDivisionUser($json['division_user']);
    }
}

class CobaltNetworkUser {
    public string $first_name;
    public string $last_name;
    public string $email;
    public int $rating;
    public string $region;
    public string $division;
    public ?string $subdivision;
    public int $pilot_rating;
    public int $military_rating;

    function __construct(array $json) {
        $this->first_name = $json['first_name'];
        $this->last_name = $json['last_name'];
        $this->email = $json['email'];
        $this->rating = $json['rating'];
        $this->region = $json['region'];
        $this->division = $json['division'];
        $this->subdivision = $json['subdivision'] ?? null;
        $this->pilot_rating = $json['pilot_rating'];
        $this->military_rating = $json['military_rating'];
    }
}

class CobaltDivisionUser {
    public string $display_name;
    public int $controller_rating;
    public int $instructor_rating;
    public string $facility;
    public array $visiting_facilities;
    public ?string $discord_id;
    public ?int $last_promotion_timestamp;
    public ?int $last_transfer_timestamp;

    function __construct(array $json) {
        $this->display_name = $json['display_name'];
        $this->controller_rating = $json['controller_rating'];
        $this->instructor_rating = $json['instructor_rating'];
        $this->facility = $json['facility'];
        $this->visiting_facilities = $json['visiting_facilities'];
        $this->discord_id = $json['discord_id'];
        $this->last_promotion_timestamp = $json['last_promotion_timestamp'];
        $this->last_transfer_timestamp = $json['last_transfer_timestamp'];
    }
}