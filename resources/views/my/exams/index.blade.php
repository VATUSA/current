@extends('layout')
@section('title', 'Exam Center')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Exam Center
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#pend" aria-controls="pend" role="tab" data-toggle="tab">Pending Exams</a></li>
                        <li role="presentation"><a href="#comp" aria-controls="comp" role="tab" data-toggle="tab">Completed Exams</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="pend">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Date</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>VATUSA Student 1 Examination</td>
                                        <td>July 3, 2015</td>
                                        <td><a href="#">Take</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="comp">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Taken</th>
                                    <th>Score</th>
                                    <th>View</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>VATUSA Basic ATC Examination</td>
                                    <td>July 3, 2015</td>
                                    <td>22/30</td>
                                    <td><a href="#">View</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop