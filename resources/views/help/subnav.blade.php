<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Helpdesk
        </h3>
    </div>
    <div class="panel-body">
        <a href="/legacy/help">View My Tickets</a><br>
        <a href="/legacy/help/new">Open Ticket</a>
        <a href="/legacy/help/kb">Knowledgebase</a>
        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTickets())
            <hr>
            <a href="/legacy/help/staff">View Tickets</a><br>
        @endif
    </div>
</div>