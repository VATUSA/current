@extends('layout')
@section('title', 'View Ticket')

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
        <form class="form form-horizontal">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">Helpdesk Ticket (#{{$ticket->id}})</h3></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Submitter:</label>
                                <div class="col-sm-5">
                                    <p class="form-control-static">{{$ticket->submitter->fullname()}} ({{$ticket->cid}}
                                        / {{\App\Classes\Helper::ratingShortFromInt($ticket->submitter->rating)}})</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Facility:</label>
                                <div class="col-sm-5">
                                    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                        <select id="tFacility" class="form-control">
                                            <option value="ZHQ">VATUSA HQ</option>
                                            @foreach(\App\Facility::where('active', '1')->orWhere('id', 'ZAE')->orderBy('name')->get() as $f)
                                                <option value="{{$f['id']}}"{{($f['id']==$ticket->facility)?" selected=\"true\"":""}}>{{$f['name']}}</option>
                                            @endforeach
                                        </select> <b>* After changing, does not save without changing "Assigned To" dropdown.</b>
                                    @else
                                        <p class="form-control-static">{{\App\Facility::find($ticket->facility)->name}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Assigned To:</label>
                                <div class="col-sm-10">
                                    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                        <select id="tAssignTo" class="form-control">
                                            <option value="0">Unassigned</option>
                                            @foreach(\App\Classes\RoleHelper::getStaff($ticket->facility, true) as $s)
                                                <option value="{{$s['cid']}}"{{($s['cid']==$ticket->assigned_to)?" selected=\"true\"":""}}>{{$s['role']}}
                                                    : {{$s['name']}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        @if ($ticket->assigned_to)
                                            <p class="form-control-static">{{$ticket->assignedto->fullname()}}
                                                ($ticket->assigned_to)</p>
                                        @else
                                            <p class="form-control-static">Unassigned</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static"><a
                                                href="/help/ticket/{{$ticket->id}}/status">{{$ticket->status}}</a></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Created:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{$ticket->created_at->format('m/d/Y H:i')}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Last Updated:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{$ticket->updated_at->format('m/d/Y H:i')}}</p>
                                </div>
                            </div>
                            @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Notes:<br>(Visible to staff only)</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="notes">{{$ticket->notes}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="btn btn-primary btnNotes">Save Notes</button>
                            </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="label label-warning">{{ count($ticket->replies) + 1 }}</span>
                        Message{{(count($ticket->replies)>0)?"s":""}}
                    </div>
                    <div class="panel-body">
                        <div id="ticketReply0" class="panel panel-default">
                            <div class="panel-body">
                                <div class="col-lg-2">
                                    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                        <a href="/mgt/controller/{{$ticket->cid}}" target="_blank">
                                            @endif
                                            {{$ticket->submitter->fullname()}}
                                            @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                        </a>
                                    @endif
                                    <br>
                                    {{\App\Classes\RoleHelper::getUserRoleFull($ticket->cid, $ticket->submitter->facility)}}
                                </div>
                                <div class="col-lg-10">
                                    <span class="font-style: italic">Date: {{ $ticket->created_at->format('m/d/Y H:i') }}</span>
                                    <hr>
                                    {!! $ticket->viewbody() !!}
                                </div>
                            </div>
                        </div>
                        @foreach ($ticket->replies as $r)
                            <div id="ticketReply{{$r->id}}" class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-lg-2">
                                        @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                            <a href="/mgt/controller/{{$r->cid}}">
                                                @endif
                                                {{$r->submitter->fullname()}}
                                                @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                                            </a>
                                        @endif
                                        <br>
                                        {{\App\Classes\RoleHelper::getUserRoleFull($r->cid, $r->submitter->facility)}}
                                    </div>
                                    <div class="col-lg-10">
                                        <span class="font-style: italic">Date: {{ $r->created_at->format('m/d/Y H:i') }}</span>
                                        <hr>
                                        {!! $r->viewbody() !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @if(\Auth::user()->cid == $ticket->cid || \App\Classes\RoleHelper::isFacilityStaff(null, $ticket->facility))
            <form method="post" action="/help/ticket/{{$ticket->id}}" class="form">
                <input type="hidden" name="_token" value="{{csrf_token()}}">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Submit Reply</div>
                            <div class="panel-body">
                                <div class="form-group">
                            <textarea id="tReply" rows="5" class="form-control" name="tReply"
                                      placeholder="Response..."></textarea>
                                </div>
                                <div class="form-actions">
                                    @if ($ticket->status == "Open")
                                        <input type="submit" name="replySubmit" class="btn btn-primary" value="Reply">
                                        <input type="submit" name="replyAndCloseSubmit" class="btn btn-success"
                                               value="Reply and Close">
                                    @else
                                        <input type="submit" name="replyAndOpenSubmit" class="btn btn-info"
                                               value="Reply and Reopen">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </form>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Ticket History</div>
                        <table class="table table-striped table-response table-condensed">
                            @foreach($ticket->history as $h)
                                <tr>
                                    <td class="fit">{{$h->created_at->format('m/d/Y H:i')}}</td>
                                    <td>{{$h->entry}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
    </div>

    @if(\App\Classes\RoleHelper::isFacilityStaff(null, $ticket->facility) || \App\Classes\RoleHelper::isInstructor())
        <script type="text/javascript">

            $('#tFacility').change(function () {
                // Show Waiting Dialog
                waitingDialog.show("Loading... make sure to change \"Assigned To\" drop-down to save!", {
                    dialogsize: "sm",
                    progressType: "ogblue"
                });

                $('#tAssignTo').prop('disabled', 'disabled');
                $.ajax({
                    method: "GET",
                    url: '/ajax/help/staffc/' + $('#tFacility').val()
                }).done(function (r) {
                    $('#tAssignTo').replaceOptions($.parseJSON(r));
                    $('#tAssignTo').prop('disabled', false);
                    waitingDialog.hide();
                });
            });

            $('#tAssignTo').change(function () {
                if ($('#tassignto').val() == "-1") {
                    // Show Error Alert
                    bootbox.alert("You must change the \"Assigned To\" box to save!");
                    return;
                }
                
                // Show Waiting Dialog
                waitingDialog.show("Saving", {
                    dialogSize: "sm", 
                    progressType: "ogblue"
                });

                // Post Ticket Data
                $.ajax({
                    method: "POST",
                    url: '/help/ticket/ajax/{{$ticket->id}}',
                    data: {facility: $('#tFacility').val(), assign: $('#tAssignTo').val()}
                }).done(function () {
                    location.reload(true);
                });
            });

            $('.btnNotes').click(function() {
                // Show Waiting Dialog
                waitingDialog.show("Saving...", {
                    dialogsize: 'sm',
                    progressType: 'ogblue'
                });

                // Post Ticket Data
                $.ajax({
                    method: "POST",
                    url: '/help/ticket/ajax/{{$ticket->id}}',
                    data: { note: $('#notes').val() }
                }).done(function (r) {
                    waitingDialog.hide();
                });
            });
        </script>
    @endif
@endsection