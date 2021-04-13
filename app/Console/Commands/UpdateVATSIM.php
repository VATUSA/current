<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

define('callsign', 0);
define('cid', 1);
define('realname', 2);
define('clienttype', 3);
define('frequency', 4);
define('latitude', 5);
define('longitude', 6);
define('altitude', 7);
define('groundspeed', 8);
define('planned_aircraft', 9);
define('planned_tascruise', 10);
define('planned_depairport', 11);
define('planned_altitude', 12);
define('planned_destairport', 13);
define('server', 14);
define('protrevision', 15);
define('rating', 16);
define('transponder', 17);
define('facilitytype', 18);
define('visualrange', 19);
define('planned_revision', 20);
define('planned_flighttype', 21);
define('planned_deptime', 22);
define('planned_actdeptime', 23);
define('planned_hrsenroute', 24);
define('planned_minenroute', 25);
define('planned_hrsfuel', 26);
define('planned_minfuel', 27);
define('planned_altairport', 28);
define('planned_remarks', 29);
define('planned_route', 30);
define('planned_depairport_lat', 31);
define('planned_depairport_lon', 32);
define('planned_destairport_lat', 33);
define('planned_destairport_lon', 34);
define('atis_message', 35);
define('time_last_atis_received', 36);
define('time_logon', 37);
define('heading', 38);
define('QNH_iHg', 39);
define('QNH_Mb', 40);

class UpdateVATSIM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateVATSIM';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update VATSIM data feeds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vdata = file_get_contents("https://data.vatsim.net/v3/vatsim-data.json");
        if ($vdata == null) {
            \Log::notice("There was an error retrieving VATSIM data from server... received header:" . json_encode($http_response_header));

            return;
        }
        $vdata = json_decode($vdata, true);
        $pilots = $vdata["pilots"];
        foreach ($pilots as $pilot) {
            if (!isset($pilot["latitude"], $pilot["longitude"]) || $pilot["latitude"] < 0 || $pilot["longitude"] > -20) {
                continue;    // Only log part of our hemisphere
            }
            $planes[] = [
                'callsign' => $pilot["callsign"] ?? "",
                'cid'      => $pilot["cid"] ?? 0,
                'type'     => $pilot["flight_plan"]["aircraft_short"] ?? "",
                'dep'      => $pilot["flight_plan"]["departure"] ?? "",
                'arr'      => $pilot["flight_plan"]["arrival"] ?? "",
                'route'    => $pilot["flight_plan"]["route"] ?? "",
                'lat'      => $pilot["latitude"] ?? "",
                'lon'      => $pilot["longitude"] ?? "",
                'hdg'      => $pilot["heading"] ?? 0,
                'spd'      => $pilot["groundspeed"] ?? 0,
                'alt'      => $pilot["altitude"] ?? 0
            ];
        }

        \Cache::put("vatsim.data", json_encode($planes, JSON_NUMERIC_CHECK),  60);      // Keep 1 minute
    }
}
