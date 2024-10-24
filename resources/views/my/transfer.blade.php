@extends('layout')
@section('title', 'Facility Transfer')
@section('content')
    <div class="container" id="znyalert" hidden>
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> WARNING</strong> ZNY (New York ARTCC) currently has a substantial training backlog</br>
            By transferring here you acknowledge there may be a delay in or extended periods of time without training availability
        </div>
    </div>

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Facility Transfer
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{url("/my/transfer/do")}}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{Auth::user()->fname}} {{Auth::user()->lname}} ({{Auth::user()->urating->short}})</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Current Facility</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{\App\Classes\Helper::facShtLng(Auth::user()->facility)}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">New Facility</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="facility" onchange="checkAlerts(this);">
                                <option value="0">Select a Facility</option>
                                @foreach(\App\Models\Facility::where('active', 1)->orderby('name', 'ASC')->get() as $f)
                                <option value="{{$f->id}}">{{$f->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10"><textarea class="form-control" rows="5" name="reason" placeholder="Why would you like to transfer?"></textarea>
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
@push('scripts')
    <script type="text/javascript">
        function checkAlerts(facility) {
            if (facility.value === "ZNY") {
                document.getElementById('znyalert').hidden = false;
            }else{
                document.getElementById('znyalert').hidden = true;
            }
        }
    </script>
@endpush