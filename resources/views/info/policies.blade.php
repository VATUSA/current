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
                    <tr class="no-hover">
                        <td colspan="3"><strong>General Division</strong></td>
                    </tr>
                    <tr>
                        <td>DP001 &mdash; Division General Policy</td>
                        <td>06/01/2018</td>
                        <td><a href="/docs/DP001.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>DP003 &mdash; Event Submission Policy</td>
                        <td>01/01/2020</td>
                        <td><a href="/docs/DP003.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>GO 32020.1 &mdash; Temporary Suspension of Live Events</td>
                        <td>12/13/2020</td>
                        <td><a href="/docs/GO-320201.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>GO 61720 &mdash; C3 Senior Controller Program</td>
                        <td>12/26/2020</td>
                        <td><a href="/docs/GO-61720.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>7210.35A &mdash; VATUSA Air Traffic Control System Command Center</td>
                        <td>08/16/2020</td>
                        <td><a href="/docs/7210.35A.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>7210.351 &mdash; VATUSA Authorized Command Center Callsigns</td>
                        <td>08/16/2020</td>
                        <td><a href="/docs/7210.351.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>7210.352 &mdash; ACE Team Policy and Procedures</td>
                        <td>01/01/2021</td>
                        <td><a href="/docs/7210.352.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>VATUSA Organizational Chart</td>
                        <td>11/01/2020</td>
                        <td><a href="/docs/orgchart.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr class="no-hover">
                        <td colspan="3"><strong>Training Administration</strong></td>
                    </tr>
                    <tr>
                        <td>3120.1A &mdash; Division Interpretation on VATNA S1 Directive</td>
                        <td>06/12/2019</td>
                        <td><a href="/docs/3120.1A.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>3120.2C &mdash; Procedures for Selection, Submission, and Appointment of Instructors</td>
                        <td>01/25/2021</td>
                        @if(\Auth::check())
                            <td><a href="/docs/3120.2C.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>3120.1D &mdash; Instructor Developmental Detail Program</td>
                        <td>04/10/2020</td>
                        @if(\Auth::check())
                            <td><a href="/docs/3120.1D.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>3120.4A &mdash; Division Training Policy</td>
                        <td>07/01/2019</td>
                        <td><a href="/docs/3120.4a.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>3120.25A &mdash; Delivery and Ground Certification Statement</td>
                        <td>07/01/2019</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff()))
                            <td><a href="/docs/3120.25A.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>3120.25B &mdash; S2 Rating Review Form</td>
                        <td>07/01/2019</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff()))
                            <td><a href="/docs/3120.25B.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>3120.25C &mdash; S3 Rating Review Form</td>
                        <td>07/01/2019</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff()))
                            <td><a href="/docs/3120.25C.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>3120.25D &mdash; C1 Rating Review Form</td>
                        <td>07/01/2019</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff()))
                            <td><a href="/docs/3120.25D.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr class="no-hover">
                        <td colspan="3"><strong>Information Technology</strong></td>
                    </tr>
                    <tr>
                        <td>D1021.1B &mdash; Division Email Scheme</td>
                        <td>07/05/2019</td>
                        <td><a href="/docs/D1021.1.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>M1022B &mdash; Unified Login Scheme</td>
                        <td>07/05/2019</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/M1022.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>N1022.1 &mdash; Authentication Security Practices</td>
                        <td>05/15/2016</td>
                        <td><a href="/docs/N1022.1.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>T1000.1 &mdash; APIv2</td>
                        <td>Soon&trade;</td>
                        <td><em rel="tooltip" title="Policy is a Work In Progress">WIP</em></td>
                       {{-- @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.1.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                        --}}
                    </tr>
                    <tr>
                        <td>T1000.4 &mdash; CBT Editor</td>
                        <td>08/23/2016</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.4.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                    </tr>
                    <tr>
                        <td>Privacy Policy</td>
                        <td>02/19/2018</td>
                        <td><a href="/info/privacy" target="_blank">view</a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
