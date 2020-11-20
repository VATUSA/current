@extends('emails.layout')
@section('title','Exam Assigned')
@section('content')
    Hello {{ $student_name }},
    <br><br>
    This email is to inform you that you have been assigned exam <em>{{ $exam_name }}</em> by instructor {{ $instructor_name }}.  You have
    until {{ $end_date }} US Pacific Time to complete the examination before it expires.
    <br><br>
    <table class="button success float-center" align="center" style="border-collapse: collapse; border-spacing: 0; float: none; padding: 0; text-align: center; vertical-align: top; width: auto;">
        <tbody>
        <tr style="padding: 0; text-align: left; vertical-align: top;">
            <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top;">
                    <tbody>
                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #3adb76; border: 0px solid #3adb76; border-collapse: collapse !important; color: #fefefe; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;"><a href="https://vatusa.net/exam" style="Margin: 0; border: 0 solid #3adb76; border-radius: 3px; color: #fefefe; display: inline-block; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.3; margin: 0; padding: 8px 16px 8px 16px; text-align: left; text-decoration: none;">VATUSA Exam Center ⇾</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <br><br>
    Prior to taking the exam, be sure to read all materials assigned to you in your {{ $facility }} welcome email.
    @if($cbt_required)
        <br><br>
        Before attempting the exam, you must complete {{$cbt_facility}}'s {{$cbt_block}} Computer Based Training (CBT) course.  You
        can access that by visiting <a
            href="https://www.vatusa.net/cbt/{{$cbt_facility}}">https://www.vatusa.net/cbt/{{$cbt_facility}}</a>.
    @endif
    <br><br>
    If you have any questions, please contact your instructor.
    <br><br>
@endsection