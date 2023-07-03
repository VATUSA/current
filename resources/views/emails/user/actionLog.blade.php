@extends('emails.layout')
@section('title','New Action Log Entry')
@section('content')
    A new action log entry for <strong>{{$user->fullname()}} - {{$user->cid}}</strong> has been entered by {{$by->fullname()}} - {{$by->cid}}<br>
    <br>
    The following is the new action log entry: <br><br><em>{{$msg}}</em>
@endsection