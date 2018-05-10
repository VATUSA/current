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
        if (\Cache::has('vatsim.statusservers')) {
            $status = json_decode(\Cache::get('vatsim.statusservers'));
        } else {
            $status = file_get_contents("http://status.vatsim.net");
            $data = explode("\n", $status);
            $status = [];
            for ($i = 0 ; isset($data[$i]) ; $i++) {
                if (preg_match("/^url0=(.+)/", $data[$i], $matches)) {
                    $status[] = rtrim($matches[1]);
                }
            }
            \Cache::put("vatsim.statusservers", json_encode($status), 12 * 60 * 60); // Cache for 12 hours
        }
        $last = ['server' => null];
        if (\Storage::has('vatsim.laststatus')) {
            $last = json_decode(\Storage::get('vatsim.laststatus'));
        }
        $x = true;
        // Never reuse server
        while ($x) {
            $selection = rand(0, count($status) - 1);
            if ($status[$selection] != $last['server']) {
                $server = rtrim($status[$selection]);
                unset($x);
                break;
            }
        }
        $vdata = file_get_contents($server);
        if ($vdata == null) {
            \Log::notice("There was an error retrieving VATSIM data from server $server... received header: $http_response_header");
            return;
        }
        $vdata = explode("\n", $vdata);
        $in_clients = false;
        $planes = [];
        foreach ($vdata as $line) {
            if(preg_match('/^;/', $line) || preg_match('/^\s+/', $line) || $line == '') continue;          // Comments/blank line

            if (preg_match("/^!CLIENTS:/", $line)) { $in_clients = true; continue; }
            elseif (preg_match('/^!/', $line)) { $in_clients = false; continue; }

            if (!$in_clients) continue;

            $pdata = explode(":", $line);
            if ($pdata[clienttype] == "ATC" || !$pdata[cid]) continue;
            if ($pdata[latitude] < 0 || $pdata[longitude] > -20) continue;    // Only log part of our hemisphere
            $planes[] = [
                'callsign' => $pdata[callsign],
                'cid' => $pdata[cid],
                'type' => $pdata[planned_aircraft],
                'dep' => $pdata[planned_depairport],
                'arr' => $pdata[planned_destairport],
                'route' => $pdata[planned_route],
                'lat' => $pdata[latitude],
                'lon' => $pdata[longitude],
                'hdg' => $pdata[heading],
                'spd' => $pdata[groundspeed],
                'alt' => $pdata[altitude]
            ];
        }

        $last['server'] = $server;

        \Cache::put('vatsim.laststatus', json_encode($last), 5 * 60);   // Keep 5 minutes
        \Cache::put("vatsim.data", json_encode($planes, JSON_NUMERIC_CHECK), 5 * 60);      // Keep 5 minutes
    }
}
