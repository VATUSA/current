@extends('emails.layout')
@section('title','CERTSync Log')
@section('content')
    @foreach($log as $value)
        {!! $value !!}<br>
    @endforeach
@endsection