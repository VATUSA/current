@extends('layout')
@section('title', 'View News Post')

@section('scripts')
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    News Post
                </h3>
            </div>
            <div class="panel-body">
                <h3 id="post_title">
                    {{ $post['title'] }}
                </h3>
                <span>by {{ $authorName }} on {{ $post['post_date'] }}</span>
                <pre id="post_body">{{ $post['body'] }}</pre>
            </div>
        </div>
    </div>
@endsection