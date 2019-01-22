@extends('layout')
@section('title', 'Support Center')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('help.subnav')
            </div>
            <div class="col-md-9">
                @yield('helpcontent')
            </div>
        </div>
    </div>
@endsection