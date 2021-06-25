@extends('exams.layout')

@section('examcontent')
    Use the links to the left to navigate the exam center.
    <h3>Your Results</h3>
    <table class="table table-striped table-condensed">
        @foreach(\App\Models\ExamResults::where('cid',\Auth::user()->cid)->orderBy('date')->get() as $exam)
            <tr style="text-align: center;">
                <td style="width:20%">{{substr($exam->date, 0, 10)}}</td>
                <td style="width: 70%; text-align: left;"><a href="/exam/result/{{$exam->id}}">{{ $exam->exam_name }}</a>
                </td>
                <td{!! ($exam->passed)?" style=\"color: green\"":" style=\"color: red\"" !!}>{{$exam->score}}
                    %
                </td>
            </tr>
        @endforeach
    </table>
@endsection