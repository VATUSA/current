@extends('emails.layout')
@section('title','Member Suspension Expired')
@section('content')
    A suspension has expired for VATUSA member {$name} ({$cid}). The user has been returned to ZAE.
    Action may be required to return them to their previous facility, if desired.
@endsection<?php
