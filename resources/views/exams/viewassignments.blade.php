@extends('exams.layout')

@section('examcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Exam Assignments</h3>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th width="10%" class="text-center">Expires</th>
                    </tr>
                </thead>
                @if ($exams != null)
                    @foreach($exams as $exam)
                        <tr>
                            <td><button class="btn btn-success btnNewAssign" data-id="{{$exam->id}}">{{$exam->exam()->name}}</button>
                            <td>{{\Carbon\Carbon::parse($exam->expire_date)->format('m/d/Y')}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="2"><i>No exams assigned.</i></td></tr>
                @endif
            </table>
        </div>
    </div>
<script type="text/javascript">
    $(document).on('click', '.btnNewAssign', function() {
      $.ajax({
        method: 'post',
        url: 'https://api.vatusa.net/v2/exam/queue/' + $(this).data('id'),
        xhrFields: {
          withCredentials: true
        }
      }).done(function (resp) {
        if (resp.data.status === "OK") {
          window.location="https://exam.vatusa.net";
        } else {
          if (resp.data.msg === "CBTs are not complete") {
            bootbox.alert("There are CBT requirements assigned with this exam that have not been completed.<br><br>The name of the CBT is: <b>" + resp.responseJSON.data.cbt + "</b>.  Press OK to be taken to the CBT.", function() {
              window.location="https://www.vatusa.net/cbt/" + resp.data.cbtFacility;
            });
            return;
          }
          if (resp.data.msg === "Unauthenticated.") {
            bootbox.alert("Your session data is not valid for this request and requires you to relogin.  Press OK to continue.", function() {
              window.location="https://login.vatusa.net/?exam";
            });
            return;
          }
          bootbox.alert("There was an error " + JSON.stringify(resp.data))
        }
      }).fail(function(resp) {
        if (resp.data.msg === "CBTs are not complete") {
          bootbox.alert("There are CBT requirements assigned with this exam that have not been completed.<br><br>The name of the CBT is: <b>" + resp.data.cbt + "</b>.  Press OK to be taken to the CBT.", function() {
            window.location="https://www.vatusa.net/cbt/" + resp.data.cbtFacility;
          });
          return;
        }
        if (resp.data.msg === "Unauthenticated.") {
          bootbox.alert("Your session data is not valid for this request and requires you to relogin.  Press OK to continue.", function() {
            window.location="https://login.vatusa.net/?exam";
          });
          return;
        }
        bootbox.alert("There was an error " + JSON.stringify(resp.data))
      })
    });
</script>
@endsection
