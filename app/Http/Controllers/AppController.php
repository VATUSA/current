<?php

namespace App\Http\Controllers;

use App\Actions;
use App\Classes\Helper;
use App\Classes\SMFHelper;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Facility;
use App\Classes\RoleHelper;
use App\PushLog;
use Auth;
/**
 *
 */
class AppController extends Controller
{
  const STATUS_SUCCESS = 1;
  const STATUS_ERROR = 2;
  public $http_codes = [
    100,
    200,
    400
  ];
  public $status;
  public $raw_response;
  public $response = [];
  public $error_message = null;

  function __construct()
  {

  }

  public function getIndex($cid = null)
  {
    // Loads the send push notification page
    if(!Auth::check() || !RoleHelper::isFacilitySeniorStaff()) abort(401);
    return view('mgt.app.push', ['cid' => $cid]);
  }

  public function getLog($cid = null)
  {
    // Loads the Push Log page
    if(!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(401);
    return view('mgt.app.log', ['cid' => $cid]);
  }

  public function getPushLog()
  {
    // Checks the database and gets the log of recent push notifications
    // Max 15
    if(!RoleHelper::isVATUSAStaff()) abort(401);
    $log = PushLog::orderBy('created_at', 'DESC')->limit(15)->get();

    return view('mgt.app.log', ['log' => $log]);
  }

  public function postPush(Request $request)
  {
    // Gathers data from user form submission
    $title = $request->title;
    $msg = $request->message . ' - Sent by: ' . Auth::user()->cid;
    $user = $request->submitted_by;
    $checked = [0];

    // Store submitted data and some other stuff into array
    $data = [
      'title' => $title,
      'message' => $msg,
      'checked' => $checked[0],
      'send_to_all' => 1,
      'devices' => 'all',
      'open_url' => 0,
      'url' => null,
      'cover' => null,
      'api_version' => 4.14
    ];

    // Create the cURL request
    $postRequest = curl_init();
    curl_setopt($postRequest, CURLOPT_URL, env('IDENT_PUSH_URL'));
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($postRequest, CURLOPT_TIMEOUT, 3);
    curl_setopt($postRequest, CURLOPT_POST, true);

    $bearerToken = env('IDENT_PUSH_KEY');
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, [
      'Api-Auth-Bearer: Bearer ' . $bearerToken
    ]);

    $query = http_build_query($data);
    curl_setopt($postRequest, CURLOPT_POSTFIELDS, $query);

    $result = curl_exec($postRequest);
    $status = curl_getinfo($postRequest, CURLINFO_HTTP_CODE);
    curl_close($postRequest);

    // Add log entry into database
    $addLog = new PushLog();
    $addLog->title = $title;
    $addLog->message = $msg;
    $addLog->submitted_by = $user;
    $addLog->save();

    // Return tp Send Push page
    return redirect("/mgt/app/push")->with('pushSuccess', 'Push Notification Sent');


  }
}
