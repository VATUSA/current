@extends('emails.layout')
@section('title','Removed from Facility')
@section('content')
    Dear {{$name}} ({{$rating}}),<br><br>

    This email is to inform you that you have been removed from your facility by {{$by}}.<br>
    <br>
    The following is the reason for your removal: <br><br><em>{{$msg}}</em>

    @if($obsInactive)
        <br><br>
        Thank you for taking a step towards becoming a controller on VATSIM. To improve our onboarding process, please take a short survey about your experience. Your responses will remain anonymous.
        <br>
        <a href="https://vats.im/vatusa-exit-survey">https://vats.im/vatusa-exit-survey</a>
    @endif

    <br><br>
    If you have any questions regarding your removal, please contact {{$facid}}-atm@vatusa.net and/or vatusa2@vatusa.net.

@endsection