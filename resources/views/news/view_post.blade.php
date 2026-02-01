@extends('layout')
@section('title', 'View News Post')

@section('scripts')
    <script>
        $(document).ready(function () {
            $.ajax({
                url : '/cobalt/news/post/' + {{ $postId }},
                beforeSend: function(xhr) { xhr.withCredentials = true },
                type: 'GET'
            }).success(function (resp) {
                $('#post_title').html(resp.title);
                $('#post_body').html(resp.body);
            })
        })
    </script>
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
                <h3 id="post_title"></h3>
                <pre id="post_body">
                    Loading...
                </pre>
            </div>
        </div>
    </div>
@endsection