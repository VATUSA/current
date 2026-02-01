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
    extends Controller {
    public function getExportOverview() {
        header("Content-Type: text/csv");
        $response = "facility,atm,datm,ta,ec,fa,wm,transfers,controllers,\n";
        $facilities = Facility::where('active', 1)->orWhere('id', 'ZAE')
            ->with(['atm_user', 'datm_user', 'ta_user', 'ec_user', 'fe_user', 'wm_user'])
            ->withCount(['transfers' => function($q) { $q->where('status', 0); }, 'members'])
            ->orderBy('id')->get();

        foreach ($facilities as $facility) {
            $response .= $facility->id . ",";
            $response .= ($facility->atm_user ? $facility->atm_user->fullname() : "Vacant") . ",";
            $response .= ($facility->datm_user ? $facility->datm_user->fullname() : "Vacant") . ",";
            $response .= ($facility->ta_user ? $facility->ta_user->fullname() : "Vacant") . ",";
            $response .= ($facility->ec_user ? $facility->ec_user->fullname() : "Vacant") . ",";
            $response .= ($facility->fe_user ? $facility->fe_user->fullname() : "Vacant") . ",";
            $response .= ($facility->wm_user ? $facility->wm_user->fullname() : "Vacant") . ",";
            $response .= $facility->transfers_count . ",";
            $response .= $facility->members_count . ",\n";
        }
        return response()->make($response, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="VATUSA Overview ' . date('Y-m-d') . '.csv"'
        ]);
        //return response()->header('Content-Type', 'text/plain');
    }

    public function getExportDetails() {
        header("Content-type: text/csv");
        $response = "facility,obs,obsg30,s1,s2,s3,c1,c3,i1,i3,sup,adm,total,\n";
        $facilities = Facility::where('active', 1)->orWhere('id', 'ZAE')->orderBy('region')->orderBy('id')->get();
        $users = User::whereIn('facility', $facilities->pluck('id'))
            ->select('facility', 'rating', 'facility_join')
            ->get();

        $stats = [];

        foreach ($facilities as $fac) {
            $stats[$fac->id] = [
                'OBS' => 0, 'OBSg30' => 0, 'S1' => 0, 'S2' => 0, 'S3' => 0,
                'C1' => 0, 'C3' => 0, 'I1' => 0, 'I3' => 0, 'SUP' => 0, 'ADM' => 0,
                'total' => 0
            ];
        }

        foreach ($users as $user) {
            if (!isset($stats[$user->facility])) {
                continue;
            }
            $s = &$stats[$user->facility];
            $s['total']++;
            $rating = Helper::ratingShortFromInt($user->rating);
            switch($rating) {
                case 'OBS':
                    $joinDate = strtotime($user->facility_join);
                    if (time() - $joinDate < 60*60*24*30) {
                        $s['OBS']++;
                    } else {
                        $s['OBSg30']++;
                    }
                    break;
                case 'S1': $s['S1']++; break;
                case 'S2': $s['S2']++; break;
                case 'S3': $s['S3']++; break;
                case 'C1': $s['C1']++; break;
                case 'C3': $s['C3']++; break;
                case 'I1': $s['I1']++; break;
                case 'I3': $s['I3']++; break;
                case 'SUP': $s['SUP']++; break;
                case 'ADM': $s['ADM']++; break;
            }
        }

        foreach ($facilities as $fac) {
            $s = $stats[$fac->id];
            $response .= $fac->id . ",";
            $response .= $s['OBSg30'] . ",";
            $response .= $s['OBS'] . ",";
            $response .= $s['S1'] . ",";
            $response .= $s['S2'] . ",";
            $response .= $s['S3'] . ",";
            $response .= $s['C1'] . ",";
            $response .= $s['C3'] . ",";
            $response .= $s['I1'] . ",";
            $response .= $s['I3'] . ",";
            $response .= $s['SUP'] . ",";
            $response .= $s['ADM'] . ",";
            $response .= $s['total'] . ",\n";
        }
        return response()->make($response, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="VATUSA Detailed Report ' . date('Y-m-d') . '.csv"'
        ]);
    }

    public function getDetails(Request $request, $facility) {
        if ($facility == "overview") {
            $data = [
                'OBS' => [],
                'S1' => [],
                'S2' => [],
                'S3' => [],
                'C1' => [],
                'I1' => [],
            ];
            $facilities = Facility::where('active', 1)->get();
            $userCounts = User::whereIn('facility', $facilities->pluck('id'))
                ->select('facility', 'rating', \DB::raw('count(*) as total'))
                ->groupBy('facility', 'rating')
                ->get();

            $stats = [];
            foreach ($facilities as $fac) {
                $stats[$fac->id] = ['OBS' => 0, 'S1' => 0, 'S2' => 0, 'S3' => 0, 'C1' => 0, 'I1' => 0];
            }

            foreach ($userCounts as $count) {
                if (!isset($stats[$count->facility])) {
                    continue;
                }
                $s = &$stats[$count->facility];
                if ($count->rating == Helper::ratingIntFromShort("OBS")) $s['OBS'] += $count->total;
                if ($count->rating == Helper::ratingIntFromShort("S1")) $s['S1'] += $count->total;
                if ($count->rating == Helper::ratingIntFromShort("S2")) $s['S2'] += $count->total;
                if ($count->rating == Helper::ratingIntFromShort("S3")) $s['S3'] += $count->total;
                if ($count->rating == Helper::ratingIntFromShort("C1") || $count->rating == Helper::ratingIntFromShort("C3")) $s['C1'] += $count->total;
                if ($count->rating >= Helper::ratingIntFromShort("I1")) $s['I1'] += $count->total;
            }

            foreach ($facilities as $fac) {
                $s = $stats[$fac->id];
                $data['OBS'][] = ["y" => $s['OBS'], "label" => $fac->id];
                $data['S1'][] = ["y" => $s['S1'], "label" => $fac->id];
                $data['S2'][] = ["y" => $s['S2'], "label" => $fac->id];
                $data['S3'][] = ["y" => $s['S3'], "label" => $fac->id];
                $data['C1'][] = ["y" => $s['C1'], "label" => $fac->id];
                $data['I1'][] = ["y" => $s['I1'], "label" => $fac->id];
            }
            echo json_encode($data, JSON_HEX_APOS);
            return;
        }

        $fac = Facility::find($facility);
        if ($fac == null || ($fac->active != 1 && $fac->id != "ZAE")) {
            abort(404);
        }

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

            if ($user->rating == Helper::ratingIntFromShort("S1")) {
                $data['S1']++;
            }
            if ($user->rating == Helper::ratingIntFromShort("S2")) {
                $data['S2']++;
            }
            if ($user->rating == Helper::ratingIntFromShort("S3")) {
                $data['S3']++;
            }
            if ($user->rating == Helper::ratingIntFromShort("C1") ||
                $user->rating == Helper::ratingIntFromShort("C3")) {
                $data['C1']++;
            }
            if ($user->rating >= Helper::ratingIntFromShort("I1")) {
                $data['I1']++;
            }
            $data['total']++;
        }
        echo json_encode($data, JSON_HEX_APOS);
    }

    public function getIndex() {
        $facilities = Facility::where('active', 1)
            ->with(['atm_user', 'datm_user', 'ta_user', 'ec_user', 'fe_user', 'wm_user'])
            ->withCount(['transfers' => function($q) { $q->where('status', 0); }, 'members'])
            ->get();
        $academyCount = User::where('facility', 'ZAE')->count();
        $facilityCount = 0;
        foreach ($facilities as $facility) {
            $transfersPending[$facility->id] = $facility->transfers_count;
            $controllersCount[$facility->id] = $facility->members_count;
            $facilityCount += $controllersCount[$facility->id];
            $atms[$facility->id] = $facility->atm_user ? $facility->atm_user->fullname() : "Vacant";
            $datms[$facility->id] = $facility->datm_user ? $facility->datm_user->fullname() : "Vacant";
            $tas[$facility->id] = $facility->ta_user ? $facility->ta_user->fullname() : "Vacant";
            $ecs[$facility->id] = $facility->ec_user ? $facility->ec_user->fullname() : "Vacant";
            $fes[$facility->id] = $facility->fe_user ? $facility->fe_user->fullname() : "Vacant";
            $wms[$facility->id] = $facility->wm_user ? $facility->wm_user->fullname() : "Vacant";
        }
        return view('stats.index', compact('transfersPending', 'controllersCount',
            'atms', 'datms', 'tas', 'ecs', 'fes', 'wms',
            'facilities', 'facilityCount', 'academyCount'));
    }
}
