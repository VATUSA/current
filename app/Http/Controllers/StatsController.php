<?php

namespace App\Http\Controllers;

use App\Classes\Helper;
use App\Models\Role;
use App\Models\User;
use App\Models\Facility;
use App\Models\Transfers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class StatsController
    extends Controller
{
    public function getExportOverview()
    {
        header("Content-Type: text/csv");
        $response = "facility,atm,datm,ta,ec,fa,wm,transfers,controllers,\n";
        foreach (Facility::where('active', 1)->orWhere('id', 'ZAE')->orderBy('id')->get() as $facility) {
            $response .= $facility->id . ",";
            $response .= (($facility->atm == 0) ? "Vacant" : $facility->atm()->fullname()) . ",";
            $response .= (($facility->datm == 0) ? "Vacant" : $facility->datm()->fullname()) . ",";
            $response .= (($facility->ta == 0) ? "Vacant" : $facility->ta()->fullname()) . ",";
            $response .= (($facility->ec == 0) ? "Vacant" : $facility->ec()->fullname()) . ",";
            $response .= (($facility->fe == 0) ? "Vacant" : $facility->fe()->fullname()) . ",";
            $response .= (($facility->wm == 0) ? "Vacant" : $facility->wm()->fullname()) . ",";
            $response .= Transfers::where('to', $facility->id)->where('status', 0)->count() . ",";
            $response .= User::where('facility', $facility->id)->count() . ",\n";
        }
        return response()->make($response, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="VATUSA Overview ' . date('Y-m-d') . '.csv"'
        ]);
        //return response()->header('Content-Type', 'text/plain');
    }

    public function getExportDetails()
    {
        header("Content-type: text/csv");
        $response = "facility,obs,obsg30,s1,s2,s3,c1,c3,i1,i3,sup,adm,total,\n";
        foreach (Facility::where('active', 1)->orWhere('id', 'ZAE')->orderBy('region')->orderBy('id')->get() as $fac) {
            $response .= $fac->id . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("OBS"))->whereRaw("DATE_ADD(`facility_join`, INTERVAL 30 DAY) < NOW()")->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("OBS"))->whereRaw("DATE_ADD(`facility_join`, INTERVAL 30 DAY) >= NOW()")->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S1"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S2"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S3"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("C1"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("C3"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("I1"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("I3"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("SUP"))->count() . ",";
            $response .= User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("ADM"))->count() . ",";
            $response .= User::where('facility', $fac->id)->count() . ",\n";
        }
        return response()->make($response, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="VATUSA Detailed Report ' . date('Y-m-d') . '.csv"'
        ]);
    }

    public function getDetails(Request $request, $facility)
    {
        if ($facility == "overview") {
            $data = [
                'OBS' => [],
                'S1' => [],
                'S2' => [],
                'S3' => [],
                'C1' => [],
                'I1' => [],
            ];
            foreach (Facility::where('active', 1)->get() as $fac) {
                $data['OBS'][] = ["y" => User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("OBS"))->count(), "label" => $fac->id];
                $data['S1'][] = ["y" => User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S1"))->count(), "label" => $fac->id];
                $data['S2'][] = ["y" => User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S2"))->count(), "label" => $fac->id];
                $data['S3'][] = ["y" => User::where('facility', $fac->id)->where('rating', Helper::ratingIntFromShort("S3"))->count(), "label" => $fac->id];
                $data['C1'][] = ["y" => User::where('facility', $fac->id)->where(function ($query) {
                    $query->where('rating', Helper::ratingIntFromShort("C1"))
                        ->orWhere('rating', Helper::ratingIntFromShort("C3"));
                })->count(), "label" => $fac->id];
                $data['I1'][] = ["y" => User::where('facility', $fac->id)->where('rating', '>=', Helper::ratingIntFromShort("I1"))->count(), "label" => $fac->id];
            }
            echo json_encode($data, JSON_HEX_APOS);
            return;
        }

        $fac = Facility::find($facility);
        if ($fac == null || ($fac->active != 1 && $fac->id != "ZAE")) abort(404);

        $data = [
            'id' => $fac->id,
            'name' => $fac->name,
            'useULS' => (($fac->uls_return != "") ? "1" : "0"),
            'OBS' => 0,
            'OBSg30' => 0,
            'S1' => 0,
            'S2' => 0,
            'S3' => 0,
            'C1' => 0,
            'I1' => 0,
            'total' => 0,
        ];
        $users = User::where("facility", $fac->id)->get();
        foreach ($users as $user) {
            if ($user->rating == Helper::ratingIntFromShort("OBS")) {
                $now = time();
                $jdate = strtotime($user->facility_join);
                if ($now - $jdate >= 60 * 60 * 24 * 30) {
                    $data['OBSg30']++;
                } else {
                    $data['OBS']++;
                }
            }

            if ($user->rating == Helper::ratingIntFromShort("S1")) $data['S1']++;
            if ($user->rating == Helper::ratingIntFromShort("S2")) $data['S2']++;
            if ($user->rating == Helper::ratingIntFromShort("S3")) $data['S3']++;
            if ($user->rating == Helper::ratingIntFromShort("C1") ||
                $user->rating == Helper::ratingIntFromShort("C3")) $data['C1']++;
            if ($user->rating >= Helper::ratingIntFromShort("I1")) $data['I1']++;
            $data['total']++;
        }
        echo json_encode($data, JSON_HEX_APOS);
    }

    public function getIndex()
    {
        $zae = Facility::where('id', 'ZAE')->get();
        $west = Facility::where('active', 1)->where('region', 8)->orderBy('name')->get();
        $midwest = Facility::where('active', 1)->where('region', 6)->orderBy('name')->get();
        $northeast = Facility::where('active', 1)->where('region', 7)->orderBy('name')->get();
        $south = Facility::where('active', 1)->where('region', 5)->orderBy('name')->get();
        $facilities = Facility::where('active', 1)->orWhere('id', 'ZAE')->get();
        $us5 = Role::where('facility', 'ZHQ')->where('role', 'US5')->first();
        $us6 = Role::where('facility', 'ZHQ')->where('role', 'US6')->first();
        $us7 = Role::where('facility', 'ZHQ')->where('role', 'US7')->first();
        $us8 = Role::where('facility', 'ZHQ')->where('role', 'US8')->first();
        $regions[0] = $regions[5] = $regions[6] = $regions[7] = $regions[8] = 0;
        foreach ($facilities as $facility) {
            $transfersPending[$facility->id] = Transfers::where('to', $facility->id)->where('status', 0)->count();
            $controllersCount[$facility->id] = User::where('facility', $facility->id)->count();
            $regions[$facility->region] += $controllersCount[$facility->id];
            if ($facility->atm == 0) {
                $atms[$facility->id] = "Vacant";
            } else {
                $atms[$facility->id] = $facility->atm()->fullname();
            }
            if ($facility->datm == 0) {
                $datms[$facility->id] = "Vacant";
            } else {
                $datms[$facility->id] = $facility->datm()->fullname();
            }
            if ($facility->ta == 0) {
                $tas[$facility->id] = "Vacant";
            } else {
                $tas[$facility->id] = $facility->ta()->fullname();
            }
            if ($facility->ec == 0) {
                $ecs[$facility->id] = "Vacant";
            } else {
                $ecs[$facility->id] = $facility->ec()->fullname();
            }
            if ($facility->fe == 0) {
                $fes[$facility->id] = "Vacant";
            } else {
                $fes[$facility->id] = $facility->fe()->fullname();
            }
            if ($facility->wm == 0) {
                $wms[$facility->id] = "Vacant";
            } else {
                $wms[$facility->id] = $facility->wm()->fullname();
            }
        }
        return view('stats.index', compact('zae',
            'west', 'midwest', 'northeast', 'south',
            'us5', 'us6', 'us7', 'us8',
            'transfersPending', 'controllersCount',
            'atms', 'datms', 'tas', 'ecs', 'fes', 'wms',
            'regions'));
    }
}
