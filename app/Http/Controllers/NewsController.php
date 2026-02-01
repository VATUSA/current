<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CobaltAPIHelper;
use Illuminate\Http\Request;
use App\Models\User;

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
        $post = CobaltAPIHelper::getNewsPost($postId);
        $author = User::find($post['author_cid']);
        $authorName = $author->fullname();
        $canDeletePost = $post['author_cid'] == \Auth::user()->cid || AuthHelper::authACL()->canManageNews();
        return view('news.view_post', compact('post', 'authorName', 'canDeletePost'));
    }
}