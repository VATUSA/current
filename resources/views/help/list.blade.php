@extends('layout')
@section('title', 'Support Center')

@section('content')
    <div class="container">
        @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isVATUSAStaff())
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="/help/ticket/myassigned" class="btn btn-primary">My Assigned Tickets</a>
                    <a href="/help/ticket/open" class="btn btn-success">Open Tickets</a>
                    <a href="/help/ticket/closed" class="btn btn-info">Closed Tickets</a>
                    <a href="/help/ticket/search" class="btn btn-warning">Search Tickets</a>
                </div>
            </div>

            <hr>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3 class="panel-title">{{$status}} Tickets</h3></div>
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th class="fit">ID</th>
                                @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <th class="fit">Facility</th>
                                @endif
                                <th>Subject</th>
                                <th class="fit">Submitter</th>
                                <th class="fit">Assigned To</th>
                                <th class="fit">Opened</th>
                                <th class="fit">Last Updated</th>
                                <th class="fit">Last Replier</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($tickets)==0)
                            <tr><td colspan="{{(\App\Classes\RoleHelper::isVATUSAStaff()?"8":"7")}}" class="text-center"><i>No {{$status}} to display</i></td></tr>
                        @else
                            @foreach($tickets as $ticket)
                                <tr class="clickable-row ticket-{{strtolower($ticket->status)}}" data-href="/help/ticket/{{$ticket->id}}">
                                    <td>{{$ticket->id}}</td>
                                    @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                    <td>{{$ticket->facility}}</td>
                                    @endif
                                    <td>{{$ticket->subject}}</td>
                                    <td>{{$ticket->submitter->fullname()}}</td>
                                    <td>{{($ticket->assigned_to)?$ticket->assignedto->fullname():"Unassigned"}}</td>
                                    <td>{{$ticket->created_at->format('m/d/Y H:i')}}</td>
                                    <td>{{$ticket->updated_at->format('m/d/Y H:i')}}</td>
                                    <td>{{($ticket->lastreplier())?$ticket->lastreplier():"None"}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <br>Rows with green backgrounds are closed tickets.
                </div>
            </div>
        </div>
    </div>
@endsection