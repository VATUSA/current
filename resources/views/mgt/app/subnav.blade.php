<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">
      iDENT App Management
    </h3>
  </div>
  <div class="panel-body">
    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff())
      <a href="/mgt/app/push">Send Push Notification</a><br>
    @endif
    @if(\App\Classes\RoleHelper::isVATUSAStaff())
      <a href="/mgt/app/log">Push Log</a>
    @endif
  </div>
</div>
