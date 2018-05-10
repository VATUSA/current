@extends('layout')
@section('title', 'Exam Center')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                @include('exams.subnav')
            </div>
            <div class="col-md-9">
                @yield('examcontent')
            </div>
        </div>
    </div>
@endsection