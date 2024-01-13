@extends('layout')
@section('title', 'DICE Team')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    DICE Team
                </h3>
            </div>
            <div class="panel-body">
                <strong>What is the DICE Team?</strong>
                <p>The VATUSA Division Instruction for Controller Education (DICE) Team is a group of instructor-rated
                    members who have volunteered their time for the purpose of providing a standard training flow for
                    developmental controllers, available to facilities in need of instructional assistance.
                    Facilities in need of the DICE Team should contact the
                    Deputy Director Training Services (VATUSA3) to request more information. </p>
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
                    @foreach(\App\Models\Role::where('role','DICE')->orderBy('cid')->get() as $ace)
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
