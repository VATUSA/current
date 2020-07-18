@extends('layout')
@section('title', 'Push Notification Log')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        @include('mgt.app.subnav')
      </div>
      <div class="col-md-9">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Push Notification Log (Last 15)</h3>
          </div>
          <div class="panel-body">
            <div>
              <table class="table table-hover table-condensed tablesorter">
                <thead>
                  <tr>
                    <th>Date/Time Submitted</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Submitted By</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($log as $l)
                  <tr>
                    <td>{{$l->created_at}}</td>
                    <td>{{$l->title}}</td>
                    <td>{{$l->message}}</td>
                    <td>{{\App\Classes\Helper::nameFromCID($l->submitted_by)}} ({{$l->submitted_by}})</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>



@stop
