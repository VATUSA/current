@extends('exams.layout')

@section('examcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                @if(\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isAcademyStaff())
                <select id="fac" class="mgt-sel">
                    @foreach(\App\Facility::where('active', 1)->orWhere('id','ZAE')->orderBy('name')->get() as $f)
                        <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                    @endforeach
                </select>
                @endif
                {{$fac}} Exam Assignments</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <table class="table" width="100%" class="blacklinktable">
                    <thead>
                        <tr>
                            <th width="23%">User</th>
                            <th width="22%">Instructor</th>
                            <th width="22%">Assigned</th>
                            <th width="22%">Expires</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                @foreach($exams as $exam)
                        <tr>
                            <td colspan="5"><center><strong>{{$exam->name}}</strong></center></td>
                        </tr>
                    <?php $flag = 0; ?>
                    @foreach(\App\ExamReassignment::where('exam_id', $exam->id)->get() as $reassign)
                            <?php $flag = 1; ?>
                        <tr class="warning" id="re{{$reassign->id}}">
                            <td><a href="/mgt/controller/{{$reassign->cid}}" data-toggle="tooltip" data-placement="bottom" title="{{$reassign->cid}}">{{\App\Classes\Helper::nameFromCID($reassign->cid)}}</a></td>
                            <td><a href="/mgt/controller/{{$reassign->instructor_id}}" data-toggle="tooltip" data-placement="bottom" title="{{$reassign->instructor_id}}">{{\App\Classes\Helper::nameFromCID($reassign->instructor_id)}}</a></td>
                            <td colspan="2">Reassign on {{date("M j, Y", strtotime($reassign->reassign_date))}}</td>
                            <td>@if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $exam->facility_id) || $exam->instructor_id == \Auth::user()->cid) <i class="fas fa-trash-alt text-danger" onClick="deleteReassign({{$reassign->id}})"></i> @endif</td>
                        </tr>
                    @endforeach
                    @foreach(\App\ExamAssignment::where('exam_id', $exam->id)->get() as $assign)
                            <?php $flag = 1; ?>
                        <tr id="as{{$assign->id}}">
                            <td><a href="/mgt/controller/{{$assign->cid}}" data-toggle="tooltip" data-placement="bottom" title="{{$assign->cid}}">{{\App\Classes\Helper::nameFromCID($assign->cid)}}</a></td>
                            <td><a href="/mgt/controller/{{$assign->instructor_id}}" data-toggle="tooltip" data-placement="bottom" title="{{$assign->instructor_id}}">{{\App\Classes\Helper::nameFromCID($assign->instructor_id)}}</a></td>
                            <td>{{date("M j, Y", strtotime($assign->assigned_date))}}</td>
                            <td>{{date("M j, Y", strtotime($assign->expire_date))}}</td>
                            <td>@if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $exam->facility_id) || $exam->instructor_id == \Auth::user()->cid) <i class="fas fa-trash-alt text-danger" onClick="deleteAssign({{$assign->id}})"></i> @endif</td>
                        </tr>
                    @endforeach
                    @if ($flag == 0)
                        <tr><td colspan="5">No assignments for this exam.</td></tr>
                    @endif
                @endforeach
                </table>
                * Yellow background signify a scheduled reassignment.  If row turns red after trying to delete it, an error occurred.  Please try again later or contact Data Services.
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#fac').change(function() {
            window.location = "/exam/view/" + $('#fac').val();
        });
        function deleteReassign(id) {
            bootbox.confirm("Are you sure you want to delete this assignment?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'DELETE',
                        url: '/exam/reassignment/' + id,
                        success: function() {
                            $('#re' + id).remove();
                        },
                        error: function() {
                            $('#re' + id).attr('class','danger');
                        }
                    });
                }
            });
        }

        function deleteAssign(id)
        {
            bootbox.confirm("Are you sure you want to delete this assignment?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'DELETE',
                        url: '/exam/assignment/' + id,
                        success: function () {
                            $('#as' + id).remove();
                        },
                        error: function () {
                            $('#as' + id).attr('class', 'danger');
                        }
                    });
                }
            });
        }
    </script>
@endsection