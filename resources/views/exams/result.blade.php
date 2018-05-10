@extends('exams.layout')

@section('examcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Exam Result</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    @if (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
                        <h4><a href="/mgt/controller/{{$user->cid}}">{{$user->fullname()}}
                                ({{$user->cid}})</a></h4>
                    @else
                        {{$user->fullname()}} ({{$user->cid}})
                    @endif<br>Exam: {{$result->exam_name}}<br>Score: {{$result->score}}
                    % {{($result->passed ? "Passed" : "Not Passed")}}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <?php $i = 1; ?>
                        @foreach ($resultdata as $data)
                            <tr>
                                @if ($result->exam->answer_visibility == "all" || ($result->exam->answer_visibility == "all_passed" && $result->passed) || (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff()))
                                    <td style="width: 20px;" rowspan="{!! ($data->is_correct)?"2":"3" !!}">{{$i}}</td>
                                @else
                                    <td style="width: 20px;" {!! ($data->is_correct)?"":"rowspan=\"2\"" !!}>{{$i}}</td>
                                @endif
                                @if ($data->is_correct == 1)
                                    <td style="width: 20px;" class="text-success"><i class="fa fa-check"></i></td>
                                @else
                                    <td style="width: 20px;" class="text-danger"><i class="fa fa-times"></i></td>
                                @endif
                                <td>{!! $data->question !!}
                                    @if ((\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff()))
                                        {!! (($data->notes)?'<br><br><b>Note:</b> '.$data->notes.'</p>':"") !!}
                                    @endif
                                </td>
                                    @if ($result->exam->answer_visibility == "all" || ($result->exam->answer_visibility == "all_passed" && $result->passed) || (\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff()))
                            </tr>
                            <tr>
                                <td colspan="2">Correct answer: {{$data->correct}}</td>
                                @endif
                                @if (!$data->is_correct)
                            </tr>
                            <tr>
                                <td colspan="2">User's Answer: {{$data->selected}}</td>
                                @endif
                            </tr>
                            <?php $i++; ?>
                        @endforeach
                        @if ($i == 1)
                            <tr>
                                <td>No exam data to show.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection