<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\tmu_facilities;
use App\tmu_colors;
use App\tmu_maps;
use App\TMUNotice;
use GuzzleHttp\Client as API;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TMUController
    extends Controller
{
    function getCoords($fac)
    {
        $fac = tmu_facilities::find($fac);
        if (!$fac) {
            abort(404);
        }
        $coords = json_decode($fac->coords);
        $gcoords = [];
        for ($i = 0; $i < count($coords); $i++) {
            if (gettype($coords[$i][0]) === "integer") {
                $coords[$i][0] = sprintf("%.2f", $coords[$i][0]);
            }
            if (gettype($coords[$i][1]) === "integer") {
                $coords[$i][1] = sprintf("%.2f", $coords[$i][1]);
            }
            $gcoords[] = [$coords[$i][1], $coords[$i][0]]; // GeoJSON is backward!
        }

        $geo = [
            'type'       => 'Feature',
            'properties' => ['facility' => "yes"],
            'geometry'   => [
                'type'        => 'Polygon',
                'coordinates' => [
                    $gcoords
                ]
            ]
        ];

        return json_encode($geo, JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
    }

    function getMapDark($fac)
    {
        return $this->getMap($fac, true);
    }

    function getMap($fac, $dark = false)
    {
        $fac = tmu_facilities::find($fac);
        if (!$fac) {
            abort(404);
        }
        $coords = json_decode($fac->coords);
        $min_lat = $min_lon = $max_lat = $max_lon = null;
        $gcoords = [];
        for ($i = 0; $i < count($coords); $i++) {
            if (gettype($coords[$i][0]) === "integer") {
                $coords[$i][0] = sprintf("%.5f1", $coords[$i][0]);
            }
            if (gettype($coords[$i][1]) === "integer") {
                $coords[$i][1] = sprintf("%.5f1", $coords[$i][1]);
            }
            $gcoords[] = [$coords[$i][1], $coords[$i][0]]; // GeoJSON is backward!
            if (!isset($min_lat)) {
                $min_lat = $coords[$i][0];
                $min_lon = $coords[$i][1];
                $max_lat = $coords[$i][0];
                $max_lon = $coords[$i][1];
            } else {
                if ($coords[$i][0] < $min_lat) {
                    $min_lat = $coords[$i][0];
                } elseif ($coords[$i][0] > $max_lat) {
                    $max_lat = $coords[$i][0];
                }

                if ($coords[$i][1] < $min_lon) {
                    $min_lon = $coords[$i][1];
                } elseif ($coords[$i][1] > $max_lon) {
                    $max_lon = $coords[$i][1];
                }
            }
        }

        if ($dark) {
            $default = "white";
        } else {
            $default = "black";
        }
        $c = tmu_colors::find($fac->id);
        $colors = [];
        if ($c) {
            if ($dark) {
                $this->genColor($c->black, "white", $colors);
            } else {
                $this->genColor($c->black, "black", $colors);
            }
            $this->genColor($c->brown, "brown", $colors);
            $this->genColor($c->red, "red", $colors);
            $this->genColor($c->blue, "blue", $colors);
            $this->genColor($c->gray, "gray", $colors);
            $this->genColor($c->green, "green", $colors);
            $this->genColor($c->lime, "lime", $colors);
            $this->genColor($c->cyan, "cyan", $colors);
            $this->genColor($c->orange, "orange", $colors);
            $this->genColor($c->purple, "purple", $colors);
            $this->genColor($c->yellow, "yellow", $colors);
            $this->genColor($c->violet, "violet", $colors);
        }

        $geo = [
            'type'       => 'Feature',
            'properties' => ['facility' => "yes"],
            'geometry'   => [
                'type'        => 'Polygon',
                'coordinates' => [
                    $gcoords
                ]
            ]
        ];
        $coords_geoJSON = json_encode($geo, JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
        $min = [$min_lat, $min_lon];
        $max = [$max_lat, $max_lon];

        return view('tmu.tmu', [
            'coords_array'   => json_encode($coords, JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION),
            'coords_geoJSON' => $coords_geoJSON,
            'min'            => $min,
            'max'            => $max,
            'default'        => $default,
            'colors'         => json_encode($colors),
            'fac'            => $fac->id,
            'facname'        => $fac->name,
            'dark'           => $dark
        ]);
    }

    function genColor($apts, $color, &$colors)
    {
        global $default;

        if (strlen($apts) == 0) {
            return;
        } elseif (strlen($apts) == 4) {
            $colors[$apts] = $color;
        } elseif ($apts == "default") {
            $default = $color;
        } else {
            $apts = explode(",", $apts);
            foreach ($apts as $apt) {
                if ($apt == "default") {
                    $default = $color;
                } else {
                    $colors[$apt] = $color;
                }
            }
        }
    }

    function getMgtIndex($fac = null)
    {
        if (!Auth::check()) {
            abort(401);
        }
        if ($fac == null) {
            if (Auth::user()->facility == "ZHQ") {
                $fac = "ZAB";
            } else {
                $fac = Auth::user()->facility;
            }
        }
        if (!(\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isMentor())) {
            abort(401);
        }

        $tmufac = tmu_facilities::where('id', $fac)->orWhere('parent', $fac)->orderBy('id')->get();

        $notices = TMUNotice::where(function ($q) {
            $q->where('expire_date', '>=', \Illuminate\Support\Carbon::now('utc'));
            $q->orWhereNull('expire_date');
        })->orderBy('priority', 'DESC')
            ->orderBy('start_date', 'DESC')
            ->orderBy('tmu_facility_id')
            ->whereIn('tmu_facility_id', $tmufac->pluck('id'))->get();

        return view('tmu.mgt', ['facilities' => $tmufac, 'fac' => $fac, 'facname' => $fac, 'notices' => $notices]);
    }

    function postMgtCoords(Request $request, $ofac = null)
    {
        if (!Auth::check()) {
            abort(401);
        }

        if ($ofac == null) {
            $fac = Auth::user()->facility;
            $ofac = $fac;
        } else {
            $fac = $ofac;
        }

        $tmufac = tmu_facilities::find($ofac);
        if (!$tmufac) {
            abort(404);
        }

        if (Auth::user()->facility != $ofac) {
            // Might be child... get parent facility
            $fac = $tmufac->parent;
        }
        if (!RoleHelper::isFacilitySeniorStaff(null, $fac) && !RoleHelper::hasRole(Auth::user()->cid, $fac,
                "WM") && !RoleHelper::hasRole(Auth::user()->cid, $fac, "FE")) {
            abort(401);
        }

        $data = json_decode($request->input("coords"));
        if ($data === null) {
            return redirect("/mgt/tmu/$fac")->with("error", "Invalid coordinate format received.");
        }

        $tmufac->coords = $request->input("coords");
        $tmufac->save();

        return redirect("/mgt/tmu")->with("success",
            "Facility coordinates saved. <a href='/tmu/map/{$ofac}'>Click here</a> to view the map.");
    }

    function getMgtColors($ofac = null)
    {
        if (!Auth::check()) {
            abort(401);
        }
        if ($ofac == null) {
            if (Auth::user()->facility == "ZHQ") {
                $fac = "ZAB";
            } else {
                $fac = Auth::user()->facility;
            }
        }

        $tmufac = tmu_facilities::find($ofac);
        if (!$tmufac) {
            abort(404);
        }
        if (Auth::user()->facility != $ofac) {
            // Might be child... get parent facility
            $fac = $tmufac->parent;
        } else {
            $fac = $ofac;
        }

        if (!RoleHelper::isFacilitySeniorStaff(null, $fac) && !RoleHelper::hasRole(Auth::user()->cid, $fac,
                "WM") && !RoleHelper::hasRole(Auth::user()->cid, $fac, "FE")) {
            abort(401);
        }

        $colors = tmu_colors::find($ofac);
        if (!$colors) {
            $colors = new tmu_colors();
            $colors->id = $ofac;
            $colors->save();
        }

        return view('tmu.mgt_colors', ['colors' => $colors, 'facname' => $tmufac->name, 'fac' => $tmufac->id]);
    }

    function postMgtColors(Request $request, $ofac = null)
    {
        if (!Auth::check()) {
            abort(401);
        }

        if ($ofac == null) {
            $fac = Auth::user()->facility;
        } else {
            $fac = $ofac;
        }

        $tmufac = tmu_facilities::find($fac);
        if (!$tmufac) {
            abort(404);
        }

        if (Auth::user()->facility != $ofac) {
            // Might be child... get parent facility
            $fac = $tmufac->parent;
        }
        if (!RoleHelper::isFacilitySeniorStaff(null, $fac) && !RoleHelper::hasRole(Auth::user()->cid, $fac,
                "WM") && !RoleHelper::hasRole(Auth::user()->cid, $fac, "FE")) {
            abort(401);
        }

        $colors = tmu_colors::find($tmufac->id);

        $appcolors = [
            'black',
            'brown',
            'blue',
            'gray',
            'green',
            'lime',
            'cyan',
            'orange',
            'red',
            'purple',
            'yellow',
            'violet'
        ];
        foreach ($appcolors as $cl) {
            $colors->{$cl} = str_replace(" ", "", $request->input($cl));
        }
        $colors->save();

        return redirect("/mgt/tmu" . (($ofac) ? "/$ofac" : $fac) . "/colors")->with("success",
            "TMU Map Colors saved. <a href=\"/tmu/$ofac\">Click here</a> to view map.");
    }

    function getMgtMapping($fac, $id)
    {
        if (!RoleHelper::isFacilitySeniorStaff(null, $fac) && !RoleHelper::hasRole(Auth::user()->cid, $fac,
                "WM") && !RoleHelper::hasRole(Auth::user()->cid, $fac, "FE")) {
            abort(401);
        }

        if ($id == 0) {
            $map = new tmu_maps();
            $map->parent_facility = $fac;
            $map->name = "New Map";
            $map->save();

            return redirect("/mgt/tmu/$fac/mapping/" . $map->id);
        } else {
            $map = tmu_maps::find($id);
            if (!$map) {
                abort(404);
            }
            if ($map->parent_facility != $fac) {
                abort(401);
            }
        }

        return view('tmu.mgt_mapping', ['fac' => $fac, 'facname' => $fac, 'map' => $map]);
    }

    function postMgtMapping(Request $request, $fac, $id)
    {
        $map = tmu_maps::find($id);
        if ($fac != $map->parent_facility) {
            abort(401);
        }
        if (!RoleHelper::isFacilitySeniorStaff(null, $fac) && !RoleHelper::hasRole(Auth::user()->cid, $fac,
                "WM") && !RoleHelper::hasRole(Auth::user()->cid, $fac, "FE")) {
            abort(401);
        }

        $map->name = $request->input("name");
        $map->facilities = $request->input("facilities");
        $map->data = $request->input("mapdata");
        $map->save();

        return redirect("/mgt/tmu/$fac#mapping")->with("success", "Map " . $map->name . " saved successfully.");
    }

    public function getNotices(string $sector = null)
    {
        $notices = TMUNotice::where(function ($q) {
            $q->where('expire_date', '>=', Carbon::now('utc'));
            $q->orWhereNull('expire_date');
        })->where('start_date', '<=', Carbon::now())->orderBy('priority', 'DESC')->orderBy('tmu_facility_id')->orderBy('start_date', 'DESC');
        if ($sector) {
            $allFacs = tmu_facilities::where('id', $sector)->orWhere('parent', $sector);
            $notices = $notices->whereIn('tmu_facility_id', $allFacs->get()->pluck('id'));
        }
        $notices = $notices->paginate(20);

        $facilities = tmu_facilities::orderBy('parent', 'ASC')->orderBy('name',
            'ASC')->get();
        $facilitiesArr = [];
        foreach ($facilities as $facility) {
            $facilitiesArr[$facility->parent ?? $facility->id][] = [
                'id'   => $facility->id,
                'name' => $facility->name
            ];
        }

        return view('tmu.notices')->with(compact('notices', 'facilitiesArr', 'sector'));

    }
}
