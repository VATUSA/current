@extends('layout')
@section('title', 'Policies')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Policies
                </h3>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Policy</th>
                        <th>Date</th>
                        <th>View</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>DP001 &mdash; Division General Policy</td>
                        <td>6/01/2018</td>
                        <td><a href="/docs/DP001.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>DP003 &mdash; Event Submission Policy</td>
                        <td>03/10/2018</td>
                        <td><a href="/docs/DP003.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>Chain of Command Flow Chart</td>
                        <td>02/17/2018</td>
                        <td><a href="/docs/orgchart.pdf" target="_blank">view</td>
                    </tr>
                    <tr>
                        <td>3120.311 &mdash; Procedures for Selection, Submission, and Appointment of Instructors</td>
                        <td>02/01/2017</td>
                        <td><a href="/docs/3120.311.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>N D1021.1B &mdash; Division Email Scheme</td>
                        <td>2/26/2017</td>
                        <td><a href="/docs/D1021.1.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>M1022B &mdash; Unified Login Scheme v2</td>
                        <td>11/28/2017</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/M1022.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>N1022.1 &mdash; Authentication Security Practices</td>
                        <td>5/15/2016</td>
                        <td><a href="/docs/N1022.1.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>T1000.2 &mdash; API - Next Gen</td>
                        <td>6/25/2016</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.2.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>T1000.4 - CBT Editor</td>
                        <td>8/23/2016</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.4.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>Privacy Policy</td>
                        <td>2/19/2018</td>
                        <td><a href="/info/privacy" target="_blank">view</a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
