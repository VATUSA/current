@extends('mgt.mail.layout')
@section('mailcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Facility {{$name}} Email Template
            </h3>
        </div>
        <div class="panel-body">
            <form action="{{url("/mgt/mail/template/$template")}}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="template">Email:</label>
                    <textarea name="template" id="template" rows="40" class="form-control">{{$data}}</textarea>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" class="btn btn-success" value="Save">
                    </div>
                </div>
            </form>
            <br><br>
            Variables (used by doing &#123;&#123;variable&#124;&#124;, ie, &#123;&#123;fname&#124;&#124;):<br>
            <ul>
                @foreach ($variables as $variable)
                <li>{{$variable}}</li>
                @endforeach
            </ul><br>
            You can use blade template methods, documentation found <a href="https://laravel.com/docs/5.1/blade">here</a>. <b>PHP code is not authorized.</b>
        </div>
    </div>
@stop