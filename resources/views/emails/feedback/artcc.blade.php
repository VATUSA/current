@extends('emails.layout')
@section('title','ARTCC Feedback')
@section('content')
    <p>Feedback was submitted for your ARTCC from the vatusa.net website</p>
    <p>Submitter: {{$data->name}}<br>
        Email address: {{$data->reply}}</p>

    <p>Feedback rating:{{$data->rating}}<br>
        Feedback narrative:<br>{{$data->msg}}}<p>
@endsection