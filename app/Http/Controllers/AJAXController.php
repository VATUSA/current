<?php
namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\User;
use Illuminate\Support\Facades\DB;

class AJAXController
    extends Controller
{

    public function getNews()
    {
        //SELECT `smf_topics`.`id_topic`,FROM_UNIXTIME(`smf_messages`.`poster_time`, \"%b %e\") AS `poster_time`,`smf_messages`.`subject`
        //FROM `smf_messages`,`smf_topics` WHERE `smf_topics`.`id_board`='2' AND `smf_topics`.`id_first_msg`=`smf_messages`.`id_msg`
        //ORDER BY `smf_messages`.`poster_time` DESC LIMIT 10
        $results = DB::connection('forum')->select("SELECT `smf_topics`.`id_topic`,FROM_UNIXTIME(`smf_messages`.`poster_time`,\"%c/%e/%Y\") AS `poster_time`,`smf_messages`.`subject` FROM `smf_messages`,`smf_topics` WHERE `smf_topics`.`id_board`=47 AND `smf_topics`.`id_first_msg`=`smf_messages`.`id_msg` ORDER BY `smf_messages`.`poster_time` DESC LIMIT 10");
        $news = [];
        foreach ($results as $result) {
            $item = [
                'id' => $result->id_topic,
                'date' => $result->poster_time,
                'subject' => $result->subject
            ];
            $news[] = $item;
        }
        echo json_encode($news, JSON_HEX_APOS);
        //SELECT `eventid`,`banner`,`name`,DATE_FORMAT(`startdate`,\"".$_CONF['opt']['datetime']."\") AS `startdate`,
        //DATE_FORMAT(`enddate`,\"".$_CONF['opt']['datetime']."\") AS `enddate` FROM `events`
        //WHERE UNIX_TIMESTAMP(`enddate`) - UNIX_TIMESTAMP() >= 0 AND `active`='1' ORDER BY `startdate`,`enddate`,`name`
    }

    public function getEvents()
    {
        $results = DB::connection('forum')->select("SELECT *,DATE_FORMAT(`start_date`, \"%c/%e/%Y\") AS `eventdate` FROM smf_calendar WHERE `start_date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `start_date` ASC LIMIT 10");
        $events = [];
        foreach ($results as $result) {
            $item = [
                'id' => $result->id_topic,
                'title' => $result->title,
                'date' => $result->eventdate
            ];
            $events[] = $item;
        }
        echo json_encode($events, JSON_HEX_APOS);
    }

    public function getCID()
    {
        $search = trim(strip_tags($_GET['term']));
        if (strlen($search) >= 2) {
            $users = User::where('cid', 'LIKE', "$search%")->limit(30)->get();
            $json = array();
            foreach ($users as $user) {
                $json[] = ['label' => $user->cid . " - " . $user->fname . " " . $user->lname, 'value' => $user->cid];
            }
            return response()->json($json);
        } else {
            abort(500);
        }
    }

    public function getHelpStaffc($facility)
    {
        $staff = RoleHelper::getStaff($facility);

        $ret = [];

        $ret[] = [ 'text' => "Notice: Assign Member To Save", 'value' => -1 ];
        $ret[] = [ 'text' => "Unassigned", 'value' => 0 ];

        foreach($staff as $s) {
            $ret[] = [ 'text' => $s['role'] . ": " . $s['name'], 'value' => $s['cid'] ];
        }

        echo json_encode($ret, JSON_HEX_APOS);
    }

    public function getHelpStaff($facility)
    {
        $staff = RoleHelper::getStaff($facility);

        $ret = [];

        $ret[] = [ 'text' => "Unassigned", 'value' => 0 ];

        foreach($staff as $s) {
            $ret[] = [ 'text' => $s['role'] . ": " . $s['name'], 'value' => $s['cid'] ];
        }

        echo json_encode($ret, JSON_HEX_APOS);
    }
}
