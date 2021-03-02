<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Email Management
        </h3>
    </div>
    <div class="panel-body">
        @if(\App\Classes\RoleHelper::isFacilityStaff())
            <a href="https://mail.vatusa.net">Webmail</a><br>
            <a href="/mgt/mail/account">Manage Email(s)</a><br>
            <a href="/mgt/mail/broadcast">Broadcast</a><br>
        @endif
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff() && !\App\Classes\RoleHelper::isVATUSAStaff())
            <a href="/mgt/facility/{{\Auth::user()->facility}}#emailtemplates">Facility Email Templates</a><br>
            <a href="/mgt/mail/welcome">Facility Welcome Message</a>
        @endif
    </div>
</div>
