@extends('layout')
@section('title', 'View News Post')

@section('scripts')
    <script>
        $('#editbutton').click(function() {
            location.href = '/mgt/news/edit/{{ $post['id'] }}'
        });
        $('#deletebutton').click(function() {
            $('#postspan').html('Saving...');
            $.ajax({
                url: '/cobalt/news/post/' + {{ $post['id'] }},
                type: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify({
                    title: $('#post_title').val(),
                    body: $('#post_body').val(),
                })
            }).success(function() {
                $('#postspan').html('Deleted');
                setTimeout(function () {
                    $('#postspan').html('')
                }, 3000);
            }).error(function () {
                $('#postspan').html('Error');
                setTimeout(function () {
                    $('#postspan').html('')
                }, 3000);
            });
        });
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
                <h3 id="post_title">
                    {{ $post['title'] }}
                </h3>
                <span>by {{ $authorName }} on {{ $post['post_date'] }}</span>
                <br />
                <div id="post_body">{!! Illuminate\Support\Str::markdown($post['body']) !!}</div>
                @if($canManagePost)
                    <button class="btn btn-info" id="editbutton">Edit</button>
                    <button class="btn btn-danger" id="deletebutton">Delete</button>
                    <span class="" id="postspan"></span>
                @endif
            </div>
        </div>
    </div>
@endsection