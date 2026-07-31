<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Models\TrainingRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AJAXController extends Controller {

    public function getCID() {
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

    public function getHelpStaffc($facility) {
        $staff = RoleHelper::getStaff($facility);

        $ret = [];

        $ret[] = ['text' => "Notice: Assign Member To Save", 'value' => -1];
        $ret[] = ['text' => "Unassigned", 'value' => 0];

        foreach ($staff as $s) {
            $ret[] = ['text' => $s['role'] . ": " . $s['name'], 'value' => $s['cid']];
        }

        echo json_encode($ret, JSON_HEX_APOS);
    }

    public function getHelpStaff($facility) {
        $staff = RoleHelper::getStaff($facility);

        $ret = [];

        $ret[] = ['text' => "Unassigned", 'value' => 0];

        foreach ($staff as $s) {
            $ret[] = ['text' => $s['role'] . ": " . $s['name'], 'value' => $s['cid']];
        }

        echo json_encode($ret, JSON_HEX_APOS);
    }
}
