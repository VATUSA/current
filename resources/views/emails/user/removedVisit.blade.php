@extends('emails.layout')
@section('title','Removed from Visiting Roster')
@section('content')
    This email is to inform you that <strong>{{$name}} - {{$cid}}</strong> has been removed from the {{$facility}} Visiting Roster on the VATUSA website for the following reason:<br>
    <br><em>{{$reason}}</em><br>
    <br>
    {{$facility}} Staff: Please ensure that this change is reflected on the {{$facility}} website and other facility resources.<br><br>
    NOTE: It is the member's responsibility to request to be added back onto the {{$facility}} Visiting Roster if they become eligible again.
@endsection