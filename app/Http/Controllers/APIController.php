<?php

namespace App\Http\Controllers;

use App\Models\SoloCert;
use Illuminate\Http\Request;
use App\Classes\EmailHelper;
use App\Classes\RoleHelper;
use App\Classes\Helper;
use App\Models\Facility;
use App\Models\Transfers;
use App\Models\User;
use App\Models\TrainingProgress;

class APIController
    extends Controller
{
    public function getConnTest(Request $request, $apikey)
    {
        $data = [];
        $data['status'] = 'OK';
        $data['ip'] = $request->ip();
        if ($request->has('test')) {
            $data['debug'] = 1;
        } else {
            $data['debug'] = 0;
        }

        return json_encode($data);
    }

    // <editor-fold desc="Controller">
    public function getController($apikey, $cid)
    {
        $user = User::find($cid);
        if (!$user) {
            $data['status'] = "error";
            $data['msg'] = "User not found";
            echo json_encode($data);
            exit;
        }

        $userArray['status'] = "success";
        $userArray['fname'] = $user->fname;
        $userArray['lname'] = $user->lname;
        $userArray['facility'] = $user->facility;
        $userArray['rating'] = $user->rating;
        $userArray['join_date'] = $user->facility_join;
        $userArray['last_activity'] = $user->lastactivity->format('Y-m-d H:i:s');
        echo json_encode($userArray, JSON_HEX_APOS);
        exit;
    }
    // </editor-fold>

    // </editor-fold>

    // <editor-fold desc="Roster">

    public function getRoster($apikey, $fac = null)
    {
        if ($fac == null) {
            $facility = Facility::where('apikey', $apikey)->first();
        } else {
            $facility = Facility::find($fac);
        }

        $return = [];

        if ($facility == null) {
            $return['status'] = "error";
            $return['msg'] = "Facility not found.";
        } else {
            $return['status'] = "success";
            $return['facility']['id'] = $facility->id;
            $return['facility']['url'] = $facility->url;
            $return['facility']['name'] = $facility->name;
            $return['facility']['atm'] = $facility->atm;
            $return['facility']['datm'] = $facility->datm;
            $return['facility']['ta'] = $facility->ta;
            $return['facility']['ec'] = $facility->ec;
            $return['facility']['wm'] = $facility->wm;
            $return['facility']['fe'] = $facility->fe;
            $users = User::where('facility', $facility->id)->get();
            foreach ($users as $user) {
                $userArray = [];
                $userArray['cid'] = $user->cid;
                $userArray['fname'] = $user->fname;
                $userArray['lname'] = $user->lname;
                $userArray['email'] = $user->email;
                $userArray['rating'] = $user->rating;
                $userArray['join_date'] = $user->facility_join;
                $userArray['last_activity'] = $user->lastactivity;
                $return['facility']['roster'][] = $userArray;
            }
        }
        echo json_encode($return, JSON_HEX_APOS);
    }

    public function deleteRoster(Request $request, $apikey)
    {
        $fac = $request->fac;
        $cid = $request->cid;

        if ($fac == null) {
            $fac = Facility::where('apikey', $apikey)->first()->id;
        }
        $vars = [];
        parse_str(file_get_contents("php://input"), $vars);
        $return = [];

        $user = User::where('cid', $cid)->first();

        if (!isset($vars['msg']) && isset($vars['reason']))
            $vars['msg'] = $vars['reason'];

        if ($user == null) {
            $return['status'] = "error";
            $return['msg'] = "User not found.";
        } elseif ($user->facility != $fac) {
            $return['status'] = "error";
            $return['msg'] = "User is not part of facility.";
        } elseif (!isset($vars['by']) || !isset($vars['msg']) || $vars['msg'] == "") {
            $return['status'] = "error";
            $return['msg'] = "By and msg arguments are not optional.";
        } else {
            if (RoleHelper::isFacilitySeniorStaff($vars['by'], $fac, true)) {
                if (!$request->has('test')) {
                    $user->removeFromFacility($vars['by'], $vars['msg']);
                }
                $return['status'] = "success";
                $return['msg'] = "User removed from facility.";
            } else {
                $return['status'] = "error";
                $return['msg'] = "Access denied for " . $vars['by'] . ".";
            }
        }

        echo json_encode($return);
    }
    // </editor-fold>

    // <editor-fold desc="Solo Certs">
    public function getSolo($apikey, $cid = null)
    {
        if (!$cid) {
            $return['status'] = "error";
            $return['msg'] = "CID field required";
        } else {
            $solos = SoloCert::where('cid', $cid)->get();
            $return['status'] = "success";
            $return['solocerts'] = [];
            foreach ($solos as $solo) {
                $sarray = [];
                $sarray['position'] = $solo->position;
                $sarray['expires'] = $solo->expires;
                $return['solocerts'][] = $sarray;
            }
        }
        echo json_encode($return);
    }

    public function postSolo(Request $request, $apikey, $cid, $position)
    {
        if (!$cid || !$position) {
            $return['status'] = "error";
            $return['msg'] = "CID field required";
        } else {
            $exp = $request->input("expires", null);
            if (!$exp || !preg_match("/^\d\d\d\d-\d\d-\d\d$/", $exp)) {
                $return['status'] = "error";
                $return['msg'] = "Malformed field data or missing field.";
            } else {
                if (!$request->has('test')) {
                    $solo = new SoloCert();
                    $solo->cid = $cid;
                    $solo->position = $position;
                    $solo->expires = $exp;
                    $solo->save();
                }
                $return['status'] = "success";
                $return['msg'] = "Success";
            }
        }

        return json_encode($return);
    }

    public function deleteSolo(Request $request, $apikey, $cid, $position)
    {
        if (!$cid || !$position) {
            $return['status'] = "error";
            $return['msg'] = "Missing required field.";
        } else {
            if (!$request->has('test')) {
                $solo = SoloCert::where('cid', $cid)->where("position", $position)->first();
                $solo->delete();
            }
            $return['status'] = "success";
            $return['msg'] = "Success";
        }

        return json_encode($return);
    }

    // </editor-fold>

    // <editor-fold desc="Transfer">
    public function getTransfer($apikey, $fac = null)
    {
        if ($fac == null) {
            $fac = Facility::where('apikey', $apikey)->first()->id;
        }

        $transfers = Transfers::where('to', $fac)->where('status', 0)->get();
        $return['status'] = "success";
        if ($transfers != null) {
            foreach ($transfers as $transfer) {
                $userInfo = [];
                $userInfo['id'] = $transfer->id;
                $userInfo['cid'] = $transfer->cid;
                $user = User::where('cid', $transfer->cid)->first();
                $userInfo['fname'] = $user->fname;
                $userInfo['lname'] = $user->lname;
                $userInfo['rating'] = $user->rating;
                $userInfo['rating_short'] = Helper::ratingShortFromInt($user->rating);
                $userInfo['email'] = $user->email;
                $userInfo['from_facility'] = $transfer->from;
                $userInfo['reason'] = $transfer->reason;
                $userInfo['submitted'] = $transfer->created_at->toDateString();
                $return['transfers'][] = $userInfo;
            }
        } else {
            $return['transfers'] = [];
        }
        echo json_encode($return);
    }

    public function postTransfer(Request $request, $apikey, $id)
    {
        $transfer = Transfers::find($id);

        if ($transfer == null) {
            $return['status'] = "error";
            $return['msg'] = "Transfer not found.";
        } else {
            $by = (int)$_POST['by'];
            $to = $transfer->to;
            if (!RoleHelper::isFacilitySeniorStaff($by, $to, true)) {
                $return['status'] = "error";
                $return['msg'] = "Access denied for $by in $to";
                \Log::info("API Access denied in postTransfer($apikey, $id, $by, $to)");
            } else {
                if ($transfer->status > 0) {
                    $return['status'] = "error";
                    $return['msg'] = "Transfer already processed.";
                } else {
                    if ($_POST['action'] == "reject") {
                        if (isset($_POST['by']) && isset($_POST['reason'])) {
                            if (!$request->has('test')) {
                                $transfer->reject($by, $_POST['reason']);
                            }
                            $return['status'] = "success";
                        } else {
                            $return['status'] = "error";
                            $return['msg'] = "Arguments by and reason are required.";
                        }
                    } elseif ($_POST['action'] == "accept") {
                        if (!$request->has('test')) {
                            $transfer->accept($by);
                        }
                        $return['status'] = "success";
                    } else {
                        $return['status'] = "error";
                        $return['msg'] = "Unknown action attempt.";
                    }
                }
            }
        }
        echo json_encode($return);
    }

    // </editor-fold>

    public function postRegister($apikey)
    {
        $fac = Facility::where('apikey', $apikey)->first()->id;
        if ($fac != "ZAE") abort(401);

        $info = json_decode(base64_decode($_POST['data']));
        $user = new User();
        $user->cid = $info->user->id;
        $user->email = $info->user->email;
        $user->fname = $info->user->name_first;
        $user->lname = $info->user->name_last;
        $user->rating = $info->user->rating->id;
        $user->facility = (($info->user->division->code == "USA") ? "ZAE" : "ZZN");
        $user->facility_join = \DB::raw("NOW()");
        $user->flag_needbasic = 1;
        $user->flag_xferOverride = 0;
        $user->flag_homecontroller = (($info->user->division->code == "USA") ? 1 : 0);
        $user->save();

        if ($user->flag_homecontroller) {
            EmailHelper::sendEmail($user->email, "Welcome to VATUSA", "emails.user.join", []);
        }
    }

    // Public API

    public function getPublicEvents($ext = "json", $limit = 100)
    {
        //$ext = substr($ext, 1);
        if (is_numeric($ext) && $limit == 100) {
            // Safe to assume
            $limit = $ext;
            $ext = "json";
        }
        $ext = strtolower($ext);
        if ($limit > 100) {
            $limit = 100;
        }
        if (!in_array($ext, ["xml", "json"])) {
            $return['status'] = "error";
            $return['msg'] = "Unsupported data type";
            $ext = "json";
        } else {
            $return['status'] = "ok";
            $return['events'] = [];
            $results = \DB::connection('forum')->select("SELECT *, DATE_FORMAT(`start_date`, \"%c/%e/%Y\") AS `eventdate` FROM `smf_calendar` WHERE `start_date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `start_date` ASC LIMIT $limit");
            foreach ($results as $result) {
                $return['events'][] = [
                    'id' => $result->id_topic,
                    'title' => $result->title,
                    'date' => $result->start_date,
                    'humandate' => $result->eventdate,
                    'url' => "https://www.vatusa.net/forums/index.php?topic=" . $result->id_topic . ".0"
                ];
            }
        }
        if ($ext == "xml") {
            $xmldata = new \SimpleXMLElement('<?xml version="1.0"?><api></api>');
            static::array_to_xml($return, $xmldata);
            echo $xmldata->asXML();
        } elseif ($ext == "json") {
            echo json_encode($return, JSON_HEX_APOS);
        }
    }

    public function getPublicNews($ext = "json", $limit = 100)
    {
        //$ext = substr($ext, 1);
        if (is_numeric($ext) && $limit == 100) {
            // Safe to assume
            $limit = $ext;
            $ext = "json";
        }
        $ext = strtolower($ext);
        if ($limit > 100) {
            $limit = 100;
        }
        if (!in_array($ext, ["xml", "json"])) {
            $return['status'] = "error";
            $return['msg'] = "Unsupported data type";
            $ext = "json";
        } else {
            $return['status'] = "ok";
            $return['news'] = [];
            $results = \DB::connection('forum')->select("SELECT `smf_topics`.`id_topic`,FROM_UNIXTIME(`smf_messages`.`poster_time`,\"%c/%e/%Y\") AS `humandate`,FROM_UNIXTIME(`smf_messages`.`poster_time`,\"%Y-%m-%d\") AS `sqldate`,`smf_messages`.`subject` FROM `smf_messages`,`smf_topics` WHERE `smf_topics`.`id_board`=47 AND `smf_topics`.`id_first_msg`=`smf_messages`.`id_msg` ORDER BY `smf_messages`.`poster_time` DESC LIMIT $limit");
            foreach ($results as $result) {
                $return['news'][] = [
                    'id' => $result->id_topic,
                    'subject' => $result->subject,
                    'humandate' => $result->humandate,
                    'date' => $result->sqldate,
                    'url' => "https://www.vatusa.net/forums/index.php?topic=" . $result->id_topic . ".0"
                ];
            }
        }
        if ($ext == "xml") {
            $xmldata = new \SimpleXMLElement('<?xml version="1.0"?><api></api>');
            static::array_to_xml($return, $xmldata);
            echo $xmldata->asXML();
        } elseif ($ext == "json") {
            echo json_encode($return, JSON_HEX_APOS);
        }
    }

    public function getPublicRoster($fac, $ext = "json", $limit = null)
    {
        $f = Facility::find($fac);
        $error = 0;
        if (!$f || !$f->active) {
            $return['status'] = "error";
            $return['msg'] = "Invalid facility";
            $error = 1;
        }
        if (is_numeric($ext) && $limit == null) {
            // Safe to assume
            $limit = $ext;
            $ext = "json";
        }
        $ext = strtolower($ext);
        if (!in_array($ext, ["xml", "json"])) {
            $return['status'] = "error";
            $return['msg'] = "Unsupported data type";
            $ext = "json";
            $error = 1;
        }
        if (!$error) {
            $return['status'] = "ok";
            $return['users'] = [];
            foreach ($f->members()->orderby('rating', 'desc')->orderBy('lname', 'asc')->orderBy('fname', 'asc')->get() as $user) {
                $return['users'][] = [
                    'cid' => $user->cid,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'join_date' => $user->facility_join,
                    'promotion_eligible' => ($user->promotionEligible()) ? "1" : "0",
                    'rating' => $user->rating,
                    'rating_short' => Helper::ratingShortFromInt($user->rating)
                ];
            }
        }
        if ($ext == "xml") {
            $xmldata = new \SimpleXMLElement('<?xml version="1.0"?><api></api>');
            static::array_to_xml($return, $xmldata);
            echo $xmldata->asXML();
        } elseif ($ext == "json") {
            echo json_encode($return, JSON_HEX_APOS);
        }
    }

    public function getPublicTraining($cid, $ext = "json", $limit = null)
    {
        $user = User::find($cid);
        $error = 0;
        if (!$user) {
            $return['status'] = "error";
            $return['msg'] = "Invalid CID";
            $error = 1;
        }
        if (is_numeric($ext) && $limit == null) {
            // Safe to assume
            $limit = $ext;
            $ext = "json";
        }
        $ext = strtolower($ext);
        if (!in_array($ext, ["xml", "json"])) {
            $return['status'] = "error";
            $return['msg'] = "Unsupported data type";
            $ext = "json";
            $error = 1;
        }
        if (!$error) {
            $return['status'] = "ok";
            $return['cbts'] = [];

            //SELECT *,(SELECT date FROM training_progress WHERE training_progress.chapterid=training_chapters.id AND training_progress.cid=876594) FROM `training_chapters` ORDER BY blockid
            foreach (TrainingProgress::where('cid', $cid) as $user) {
                $return['users'][] = [
                    'cid' => $user->cid,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'join_date' => $user->facility_join,
                    'promotion_eligible' => ($user->promotionEligible()) ? "1" : "0",
                    'rating' => $user->rating,
                    'rating_short' => Helper::ratingShortFromInt($user->rating)
                ];
            }
        }
        if ($ext == "xml") {
            $xmldata = new \SimpleXMLElement('<?xml version="1.0"?><api></api>');
            static::array_to_xml($return, $xmldata);
            echo $xmldata->asXML();
        } elseif ($ext == "json") {
            echo json_encode($return, JSON_HEX_APOS);
        }
    }

    public function getPublicPlanes()
    {
        if (\Cache::has('vatsim.data'))
            echo \Cache::get('vatsim.data');
        else
            echo '[]';
    }

    private static function array_to_xml($data, &$xmldata)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key))
                $key = "item$key";
            if (is_array($value)) {
                $subnode = $xmldata->addChild($key);
                static::array_to_xml($value, $subnode);
            } else {
                $xmldata->addChild($key, htmlspecialchars($value));
            }
        }
    }

    private function error($msg)
    {
        $result = [];
        $result['status'] = "error";
        $result['msg'] = $msg;
        return json_encode($result);
    }
}
