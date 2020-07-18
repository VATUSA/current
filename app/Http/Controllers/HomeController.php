<?php namespace App\Http\Controllers;

use App\tmu_facilities;
use App\TMUNotice;
use Illuminate\Support\Carbon;

class HomeController
    extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Banner
        $banners = [];

        $results = \DB::connection('forum')->select("SELECT * , DATE_FORMAT(sc.`start_date` ,  \"%c/%e/%Y\") AS `eventdate` FROM smf_calendar AS sc LEFT JOIN smf_messages ON smf_messages.id_topic = sc.id_topic WHERE sc.`start_date` > DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY sc.id_topic ORDER BY sc.`start_date` ASC LIMIT 5");

        foreach ($results as $result) {
            if (preg_match('/\[img\]([^\[]+)\[\/img\]/i', $result->body, $matches)) {
                $banners[] = $matches[1];
                $ids[] = $result->id_topic;
            }
        }

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
                'id'   => $facility->id,
                'name' => $facility->name
            ];
        }

        return view('index', compact('banners', 'ids', 'notices', 'facilitiesArr'));
    }
}