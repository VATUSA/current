Dear {{ $student_name }},
<br><br>
This email is to inform you that you have been assigned exam {{ $exam_name }} by instructor {{ $instructor_name }}.  You have
until {{ $end_date }} US Central to complete the examination before it expires.
<br><br>
To take the exam, please visit the VATUSA Exam Center at <a href="https://www.vatusa.net/exam">https://www.vatusa.net/exam</a>.
@if($cbt_required)
    <br><br>
    Before attempting the exam, you must complete {{$cbt_facility}}'s {{$cbt_block}} Computer Based Training (CBT) course.  You
    can access that by visiting <a
            href="https://www.vatusa.net/cbt/{{$cbt_facility}}">https://www.vatusa.net/cbt/{{$cbt_facility}}</a>.
@endif
<br><br>
If you have any questions, please contact your instructor.
<br><br>
Respectfully,
The VATUSA Staff