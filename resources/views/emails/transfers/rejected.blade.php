@extends('emails.layout')
@section('title','Transfer Rejected')
@section('content')
    Dear {{$fname}} {{$lname}},<br>
    <br>
    This email is to inform you that your request to transfer to {{$facname}} ({{$facid}}) has been
    <strong>rejected</strong> by {{$by}}.
    <br><br>
    The reason specified for the rejection of your request is: <br><br><em>{{$msg}}</em>.<br>
    <br>
    If you have any questions or wish to appeal this decision, please contact either {{$facid}}-atm@vatusa.net or vatusa{{$region}}@vatusa.net.
@endsection