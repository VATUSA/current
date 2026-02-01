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
        return view('news.create_post');
    }

    public function getPost(Request $request, $postId = null) {
        if ($postId == null) abort(404);
        return view('news.view_post', compact('postId'));
    }
}