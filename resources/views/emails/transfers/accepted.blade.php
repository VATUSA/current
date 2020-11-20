@extends('emails.layout')
@section('title','Transfer Accepted')
@section('content')
    <p>A transfer request for {{$fname}} {{$lname}} ({{$cid}}) from {{ $from }} to {{ $to }} as been
        <strong>accepted</strong>.<br>
@endsection