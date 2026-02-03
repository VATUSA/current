<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\CobaltAPIHelper;
use Illuminate\Http\Request;
use App\Models\User;

class NewsController extends Controller
{

    public function getIndex(Request $request, $page = 1) {
        $posts = CobaltAPIHelper::getNewsPage($page);

        foreach ($posts as $k => $post) {
            $author = User::find($post['author_cid']);
            $post['author_name'] = $author->fullname();
            $posts[$k] = $post;
        }
        return view('news.index', compact('posts', 'page'));
    }
    public function getCreatePost(Request $request) {
        if (!AuthHelper::authACL()->canPostNews()) {
            abort(403);
        }
        return view('news.create_post');
    }

    public function getEditPost(Request $request, $id) {
        if (!AuthHelper::authACL()->canPostNews()) {
            abort(403);
        }
        $post = CobaltAPIHelper::getNewsPost($id);
        if ($post['author_cid'] != \Auth::user()->cid && !AuthHelper::authACL()->canManageNews()) {
            abort(403);
        }
        return view('news.update_post', compact('post'));
    }

    public function getPost(Request $request, $postId = null) {
        if ($postId == null) abort(404);
        $post = CobaltAPIHelper::getNewsPost($postId);
        $author = User::find($post['author_cid']);
        $authorName = $author->fullname();
        $canManagePost = \Auth::check() &&
            ($post['author_cid'] == \Auth::user()->cid || AuthHelper::authACL()->canManageNews());
        return view('news.view_post', compact('post', 'authorName', 'canManagePost'));
    }
}