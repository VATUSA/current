@extends('layout')
@section('title', 'KB Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="/help/kbe">Knowledgebase Editor</a> <i class="fa fa-angle-double-right"></i> <a href="/help/kbe/{{$cat->id}}">{{$cat->name}}</a> <i class="fa fa-angle-double-right"></i> {{($question)?$question->question:"New Question"}}
                </h3>
            </div>
            <div class="panel-body">
                <form action="/help/kbe/{{$cat->id}}/{{($question)?$question->id:0}}" method="post">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="question">Question</label>
                        <input type="text" class="form-control" id="question" name="question" placeholder="What do I do now?" value="{{($question)?$question->question:""}}">
                    </div>
                    <div class="form-group">
                        <label for="answer">Answer</label>
                        <textarea class="form-control" id="answer" name="answer" rows="10">{{($question)?$question->answer:""}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button> <button class="btn btn-danger" onClick="window.location='/help/kbe/{{$cat->id}}';">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace('answer');
        });
    </script>
@stop