@extends('layout')
@section('title', 'Email Management')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('mgt.mail.subnav')
            </div>
            <div class="col-md-9">
                @yield('mailcontent')
            </div>
        </div>
    </div>
@endsection