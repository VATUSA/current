@extends('layout')
@section('title', 'Closed Tickets')

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
                            <th colspan="4">
                                <p>Page {{$page}} of {{$pages}}</p>
                            </th>
                            <th colspan="4" class="text-right">
                                @if($page > 1)
                                    <a href="/help/ticket/closed?sort={{$sort}}&dir={{$sortdir}}&page={{$page - 1}}"
                                       class="btn btn-primary">Prev</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled" style="pointer-events: none;">Prev</a>
                                @endif
                                @if($page != $pages)
                                    <a href="/help/ticket/closed?sort={{$sort}}&dir={{$sortdir}}&page={{$page + 1}}"
                                       class="btn btn-primary">Next</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled" style="pointer-events: none">Next</a>
                                @endif
                            </th>
                        </tr>
                        <tr>
                            <th class="fit"><a
                                        href="/help/ticket/closed?sort=id&dir={{($sort == "id")?$osortdir:"desc"}}&page=1">ID
                                    @if($sort == "id")
                                        {!! ($sortdir == "asc") ? '<i class="fa fa-arrow-up"></i>' : '<i class="fa fa-arrow-down"></i>'!!}
                                    @else
                                        <i class="fa fa-arrows-v"></i>
                                    @endif
                                </a></th>
                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <th class="fit"><a
                                            href="/help/ticket/closed?sort=facility&dir={{($sort == "facility")?$osortdir:"desc"}}&page=1">Facility
                                        @if($sort == "facility")
                                            {!! ($sortdir == "asc") ? '<i class="fa fa-arrow-up"></i>' : '<i class="fa fa-arrow-down"></i>'!!}
                                        @else
                                            <i class="fa fa-arrows-v"></i>
                                        @endif
                                    </a></th>
                            @endif
                            <th>Subject</th>
                            <th class="fit">Submitter</th>
                            <th class="fit">Assigned To</th>
                            <th class="fit"><a
                                        href="/help/ticket/closed?sort=created_at&dir={{($sort == "created_at")?$osortdir:"desc"}}&page=1">Opened
                                    @if($sort == "created_at")
                                        {!! ($sortdir == "asc") ? '<i class="fa fa-arrow-up"></i>' : '<i class="fa fa-arrow-down"></i>'!!}
                                    @else
                                        <i class="fa fa-arrows-v"></i>
                                    @endif
                                </a></th>
                            <th class="fit"><a
                                        href="/help/ticket/closed?sort=updated_at&dir={{($sort == "updated_at")?$osortdir:"desc"}}&page=1">Last
                                    Updated
                                    @if($sort == "updated_at")
                                        {!! ($sortdir == "asc") ? '<i class="fa fa-arrow-up"></i>' : '<i class="fa fa-arrow-down"></i>'!!}
                                    @else
                                        <i class="fa fa-arrows-v"></i>
                                    @endif
                                </a></th>
                            <th class="fit">Last Replier</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($tickets)==0)
                            <tr>
                                <td colspan="{{(\App\Classes\RoleHelper::isVATUSAStaff()?"8":"7")}}"
                                    class="text-center"><i>No {{$status}} to display</i></td>
                            </tr>
                        @else
                            @foreach($tickets as $ticket)
                                <tr class="clickable-row ticket-{{strtolower($ticket->status)}}"
                                    data-href="/help/ticket/{{$ticket->id}}">
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
                        <tfoot>
                        <tr>
                            <td colspan="4">
                                <p>Page {{$page}} of {{$pages}}</p>
                            </td>
                            <td colspan="4" class="text-right">
                                @if($page > 1)
                                    <a href="/help/ticket/closed?sort={{$sort}}&dir={{$sortdir}}&page={{$page - 1}}"
                                       class="btn btn-primary">Prev</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled" style="pointer-events: none;">Prev</a>
                                @endif
                                @if($page != $pages)
                                    <a href="/help/ticket/closed?sort={{$sort}}&dir={{$sortdir}}&page={{$page + 1}}"
                                       class="btn btn-primary">Next</a>
                                @else
                                    <a href="#" class="btn btn-primary disabled" style="pointer-events: none">Next</a>
                                @endif
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <br>Rows with green backgrounds are closed tickets.
                </div>
            </div>
        </div>
    </div>
@endsection