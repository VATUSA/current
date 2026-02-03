@extends('layout')
@section('title', 'News Posts')

@section('scripts')
    <script>
        $.ajaxSetup({
            xhrFields: {
                withCredentials: true
            }
        });
        $('#postbutton').click(function() {
                $('#postspan').html('Saving...');
                $.ajax({
                    url: '/cobalt/news/post/{{ $post['id'] }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        title: $('#post_title').val(),
                        body: $('#post_body').val(),
                    })
                }).success(function() {
                    $('#postspan').html('Updated');
                }).error(function () {
                    $('#postspan').html('Error');
                });
        });
    </script>
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Editing Post {{ $post['id'] }}
                </h3>
            </div>
            <div class="panel-body">
                <div class="col-sm-offset-2 col-sm-10">
                    <p>Markdown is supported for the post body field.</p>
                    <p></p><a href="https://www.markdownguide.org/basic-syntax/">Basic Markdown Syntax</a> contains details about Markdown.
                    HTML is not supported.</p>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="post_title">Title</label>
                    <div class="col-sm-10">
                        <input type="text" id="post_title" class="form-control" value="{{ $post['title'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="post_body">Body</label>
                    <div class="col-sm-10">
                        <textarea  class="form-control" rows="10" id="post_body" name="body">{{ $post['body'] }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button class="btn btn-info" id="postbutton">Edit</button>
                        <span class="" id="postspan"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection