<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getIndex(Request $request) {
        if (!AuthHelper::authACL()->canPostNews()) {
            abort(403);
        }
        return view('mgt.news.index');
    }
}