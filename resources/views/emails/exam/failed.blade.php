Dear {{ $student_name }},
<br><br>
This email is to notify you that you did not pass your assigned exam.
<br><br>
Exam: {{ $exam_name }}<br>
Score: {{ $correct }}/{{ $possible }} ({{$score}}%)
<br><br>
@if($reassign > 0)
Your exam will be reassigned in {{$reassign}} days.
@else
Your exam will be reassigned by your training staff.
@endif
<br><br>
A copy of this has also been sent to your training staff.
<br><br>
Better luck next time,
The VATUSA Staff