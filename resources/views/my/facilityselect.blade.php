@extends('layout')
@section('title', 'Select Facility')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Select Facility
                </h3>
            </div>
            <div class="panel-body">
                <p>Please take your time on your selection.  Once you have selected a facility, this action
                is irrevocable and transfers are only handled in accordance with VATUSA Division Policy.</p>
                <form class="form-horizontal" action="{{secure_url("/my/select")}}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Facility</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="facility">
                                <option value="0">Select a Facility</option>
                                @foreach(\App\Facility::where('active', 1)->orderby('name', 'ASC')->get() as $f)
                                    <option value="{{$f->id}}">{{$f->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" class="btn btn-success" value="Submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop