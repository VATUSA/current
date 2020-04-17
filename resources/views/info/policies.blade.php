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
                        <td>04/15/2020</td>
                        <td><a href="/docs/GO-320201.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>GO 41520 &mdash; Temporary Suspension of Visitor Applications</td>
                        <td>04/15/2020</td>
                        <td><a href="/docs/GO-41520.pdf" target="_blank">view</a></td>
                    </tr>
                    <tr>
                        <td>Chain of Command Flow Chart</td>
                        <td>07/21/2019</td>
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
                        <td>3120.311 &mdash; Procedures for Selection, Submission, and Appointment of Instructors</td>
                        <td>03/20/2020</td>
                        @if(\Auth::check())
                            <td><a href="/docs/3120.311.pdf" target="_blank">view</a></td>
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
                        <td>Summer 2020</td>
                        <td><em rel="tooltip" title="Policy is a Work In Progress">WIP</em></td>
                       {{-- @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.1.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
                        --}}
                    </tr>
                    <tr>
                        <td>T1000.2 &mdash; APIv1 <em>(Deprecated)</em></td>
                        <td>06/25/2016</td>
                        @if(\Auth::check() && (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, 'WM') || \App\Classes\RoleHelper::isVATUSAStaff()))
                            <td><a href="/docs/T1000.2.pdf" target="_blank">view</a></td>
                        @else
                            <td>internal</td>
                        @endif
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
