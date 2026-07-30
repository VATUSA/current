<?php namespace App\Http\Controllers;

use App\Models\tmu_facilities;
use App\Models\TMUNotice;
use Illuminate\Support\Carbon;

class HomeController
    extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard to the user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        // Banners were sourced from the forums (smf_calendar/smf_messages via the
        // 'forum' DB connection), but the forums application has been retired and
        // $banners/$ids were never referenced by the view - just a dead query run
        // on every homepage load (including every readiness/liveness probe hit)
        // against a DB connection nothing else maintains.
        $banners = [];
        $ids = [];

        //TMU Notices
        $notices = TMUNotice::where(function ($q) {
            $q->where('expire_date', '>=', Carbon::now('utc'));
            $q->orWhereNull('expire_date');
        })->where('start_date', '<=', Carbon::now())->orderBy('priority', 'DESC')
            ->orderBy('tmu_facility_id')->orderBy('start_date', 'DESC')->paginate(5);

        $facilities = tmu_facilities::orderBy('parent', 'ASC')->orderBy('name',
            'ASC')->get();
        $facilitiesArr = [];
        foreach ($facilities as $facility) {
            $facilitiesArr[$facility->parent ?? $facility->id][] = [
                'id' => $facility->id,
                'name' => $facility->name
            ];
        }

        return view('index', compact('banners', 'ids', 'notices', 'facilitiesArr'));
    }
}