@extends('emails.layout')
@section('title','Exam Assigned')
@section('content')
    Hello {{ $data['student_name'] }},
    <br><br>
    This email is to inform you that you have been assigned exam <em>{{ $data['exam_name'] }}</em> by instructor {{ $data['instructor_name'] }}.  You have
    until {{ $data['end_date'] }} US Central Time to complete the examination before it expires.
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
    Prior to taking the exam, be sure to read all materials assigned to you in your {{ $data['facility'] }} welcome email.
    @if($data['cbt_required'])
        <br><br>
        Before attempting the exam, you must complete {{$data['cbt_facility']}}'s {{$data['cbt_block']}} Computer Based Training (CBT) course.  You
        can access that by visiting <a
            href="https://www.vatusa.net/cbt/{{$data['cbt_facility']}}">https://www.vatusa.net/cbt/{{$data['cbt_facility']}}</a>.
    @endif
    <br><br>
    If you have any questions, please contact your instructor.
    <br><br>
@endsection