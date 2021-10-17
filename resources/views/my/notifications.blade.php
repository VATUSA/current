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
            @php $val = $notifications['academy_exam_course_enrolled'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_course_enrolled" value="1" autocomplete="off"
                           @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_course_enrolled" value="2" autocomplete="off"
                           @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_course_enrolled" value="3" autocomplete="off"
                           @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_course_enrolled" value="0" autocomplete="off"
                           @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Academy Exam Result</td>
            @php $val = $notifications['academy_exam_result'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_result" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_result" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_result" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="academy_exam_result" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Legacy Exam Assigned</td>
            @php $val = $notifications['legacy_exam_assigned'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_assigned" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_assigned" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_assigned" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_assigned" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Legacy Exam Result</td>
            @php $val = $notifications['legacy_exam_result'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_result" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_result" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_result" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="legacy_exam_result" value="0" autocomplete="off" @if($val == 0) checked @endif>
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
            @php $val = $notifications['transfer_new'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_new" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_new" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_new" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_new" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Transfer Accepted/Denied</td>
            @php $val = $notifications['transfer_action'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_action" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_action" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_action" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_action" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Roster Removal</td>
            @php $val = $notifications['roster_removal'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="roster_removal" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="roster_removal" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="roster_removal" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="roster_removal" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Pending Transfers (more than {{ config('tattlers.transfers.maxdays', 7) }} days)</td>
            @php $val = $notifications['transfer_pending'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_pending" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_pending" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_pending" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="transfer_pending" value="0" autocomplete="off" @if($val == 0) checked @endif>
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
            @php $val = $notifications['ticket_new'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_new" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_new" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_new" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_new" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Assigned to You</td>
            @php $val = $notifications['ticket_assigned'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_assigned" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_assigned" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_assigned" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_assigned" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Reply</td>
            @php $val = $notifications['ticket_reply'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reply" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reply" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reply" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reply" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Reopened</td>
            @php $val = $notifications['ticket_reopened'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reopened" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reopened" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reopened" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_reopened" value="0" autocomplete="off" @if($val == 0) checked @endif>
                </label></td>
        </tr>
        <tr>
            <td>Ticket Closed</td>
            @php $val = $notifications['ticket_closed'] ?? 0; @endphp
            <td class="notification-setting-cell @if($val == 1) warning @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_closed" value="1" autocomplete="off" @if($val == 1) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 2) info @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_closed" value="2" autocomplete="off" @if($val == 2) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 3) success @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_closed" value="3" autocomplete="off" @if($val == 3) checked @endif>
                </label></td>
            <td class="notification-setting-cell @if($val == 0) danger @endif"><label>
                    <input class="form-control notification-setting-select" type="radio"
                           name="ticket_closed" value="0" autocomplete="off" @if($val == 0) checked @endif>
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