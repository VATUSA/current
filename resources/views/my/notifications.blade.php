<div class="container-fluid">
    <table class="table table-striped table-responsive" id="notifications-table">
        <thead>
        <tr>
            <th>Notification Type</th>
            <th><i class="fas fa-envelope"></i> Email</th>
            <th><i class="fab fa-discord"></i> Discord</th>
            <th><i class="fas fa-asterisk"></i> Both</th>
            <th><i class="fas fa-times"></i> None</th>
        </tr>
        </thead>
        <tbody>
        <tr class="notification-group">
            <td><strong>Academy/Legacy Exams</strong><br><em class="help-block">Recipients: Instructor, Student</em>
            </td>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td>Academy Exam Course Enrolled</td>
            @php $val = $notifications['academyCourseEnrolled'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyCourseEnrolled" value="1" autocomplete="off"
                           @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyCourseEnrolled" value="2" autocomplete="off"
                           @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyCourseEnrolled" value="3" autocomplete="off"
                           @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyCourseEnrolled" value="0" autocomplete="off"
                           @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Academy Exam Result</td>
            @php $val = $notifications['academyExamResult'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyExamResult" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyExamResult" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyExamResult" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academyExamResult" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Legacy Exam Assigned</td>
            @php $val = $notifications['legacyExamAssigned'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamAssigned" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamAssigned" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamAssigned" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamAssigned" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Legacy Exam Result</td>
            @php $val = $notifications['legacyExamResult'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamResult" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamResult" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamResult" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacyExamResult" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr class="notification-group">
            <td><strong>Roster Membership</strong><br><em class="help-block">Recipients: DATM, ATM, TA, RM</em></td>
            <td><i class="fas fa-envelope"></i> Email</td>
            <td><i class="fab fa-discord"></i> Discord</td>
            <td><i class="fas fa-asterisk"></i> Both</td>
            <td><i class="fas fa-times"></i> None</td>
        </tr>
        <tr>
            <td>New Transfer Request</td>
            @php $val = $notifications['transferNew'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferNew" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferNew" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferNew" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferNew" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Transfer Accepted/Denied</td>
            @php $val = $notifications['transferAction'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferAction" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferAction" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferAction" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferAction" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Roster Removal</td>
            @php $val = $notifications['rosterRemoval'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="rosterRemoval" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="rosterRemoval" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="rosterRemoval" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="rosterRemoval" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Pending Transfers (more than {{ config('tattlers.transfers.maxdays', 7) }} days)</td>
            @php $val = $notifications['transferPending'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferPending" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferPending" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferPending" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transferPending" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr class="notification-group">
            <td><strong>Support Tickets</strong><br><em class="help-block">Recipients: Assigned Staff (all staff if
                    none), Member</em></td>
            <td><i class="fas fa-envelope"></i> Email</td>
            <td><i class="fab fa-discord"></i> Discord</td>
            <td><i class="fas fa-asterisk"></i> Both</td>
            <td><i class="fas fa-times"></i> None</td>
        </tr>
        <tr>
            <td>New Ticket</td>
            @php $val = $notifications['ticketNew'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketNew" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketNew" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketNew" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketNew" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Assigned to You</td>
            @php $val = $notifications['ticketAssigned'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketAssigned" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketAssigned" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketAssigned" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketAssigned" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Reply</td>
            @php $val = $notifications['ticketReply'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReply" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReply" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReply" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReply" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Reopened</td>
            @php $val = $notifications['ticketReopened'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReopened" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReopened" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReopened" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketReopened" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Closed</td>
            @php $val = $notifications['ticketClosed'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketClosed" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketClosed" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketClosed" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticketClosed" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        </tbody>
    </table>
</div>
@push('scripts')
    <script type="text/javascript">

      $(document).ready(function () {
        $('.notification-setting-select').on('change', function (e) {
          if ($(this).val().length === 0) return
          let input = $(this),
              val   = input.val(),
              type  = input.attr('name')
          $(document.body).css({'cursor' : 'wait'});
          let color = ''
          switch (parseInt(val)) {
            case 1:
              color = 'warning'
              break
            case 2:
              color = 'info'
              break
            case 3:
              color = 'success'
              break
            case 0:
              color = 'danger'
              break
          }
          $.post('/my/ajax/notificationSetting', {type: type, option: val}, function () {
            input.parents('td').siblings().removeClass('success info warning danger')
            input.parents('td').addClass(color)
          }).fail(function () {
            swal('Error!', 'Unable to change notification setting. Please try again later.', 'error')
            input.prop('checked', false)
            input.parents('td').siblings('.success, .danger,.info,.warning').find('input').prop('checked', true)
          })
            .always(function () {
              $(document.body).css({'cursor' : 'default'});
            })
        })
      })
    </script>
@endpush