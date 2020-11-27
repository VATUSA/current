@extends('emails.layout')
@section('content')
    {!! $msg !!}
    <br>
    <em> --<br>
    This email was issued by {{$init}}</em>
@endsection