<?php namespace App\Http\Controllers;

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
     * @return Response
     */
    public function index()
    {
        $banners = [];

        $results = \DB::connection('forum')->select("SELECT * , DATE_FORMAT(sc.`start_date` ,  \"%c/%e/%Y\") AS `eventdate` FROM smf_calendar AS sc LEFT JOIN smf_messages ON smf_messages.id_topic = sc.id_topic WHERE sc.`start_date` > DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY sc.id_topic ORDER BY sc.`start_date` ASC LIMIT 5");

        foreach ($results as $result) {
            if (preg_match('/\[img\]([^\[]+)\[\/img\]/i', $result->body, $matches)) {
                $banners[] = $matches[1];
                $ids[] = $result->id_topic;
            }
        }

        return view('index', ['banners' => $banners, 'ids' => $ids]);
    }
}