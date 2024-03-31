@extends('layout')
@section('title', 'Facility Welcome Message')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Facility Welcome Email
            </h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="{{url("/mgt/mail/welcome")}}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Email:</label>
                    <div class="col-sm-10">
                        <textarea name="welcome" id="welcome" rows="40" class="form-control">{{$welcome}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" class="btn btn-success" value="Save">
                    </div>
                </div>
            </form>
            <br><br>
            Variables (used by doing %variable%, ie, %fname%):<br>
            <ul>
                <li>fname - First Name</li>
                <li>lname - Last Name</li>
            </ul>
        </div>
    </div>
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
            CKEDITOR.replace('welcome');
    </script>
@stop