<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Helpdesk
        </h3>
    </div>
    <div class="panel-body">
        <a href="/help">View My Tickets</a><br>
        <a href="/help/new">Open Ticket</a>
        <a href="/help/kb">Knowledgebase</a>
        @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
            <hr>
            <a href="/help/staff">View Tickets</a><br>
        @endif
    </div>
</div>