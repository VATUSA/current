@extends('layout')
@section('title', 'ACE Team')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    ACE Team
                </h3>
            </div>
            <div class="panel-body">
                <strong>What is the ACE Team?</strong>
                <p>The VATUSA Advanced Controller for Events (ACE) Team is a group of highly skilled advanced controllers who have volunteered to make themselves available for assisting USA Facilities with event staffing. Each member represents the best of VATUSA controlling ability and are exceptional controllers. Facilities in need of the ACE Team should contact the VATUSA Command Center Manager (VATUSA9) to schedule the team for an event.</p>
                <hr>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>CID</th>
                        <th>Name</th>
                        <th>Rating</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Role::where('role','ACE')->orderBy('cid')->get() as $ace)
                    <tr>
                        <td>{{$ace->cid}}</td>
                        <td>{{$ace->user()->first()->fullname()}}</td>
                        <td>{{\App\Classes\Helper::ratingShortFromInt($ace->user()->first()->rating)}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
