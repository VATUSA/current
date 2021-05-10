@extends('layout')
@section('title', 'Denied')

@section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="alert alert-danger">
                <strong><i class="fas fa-times"></i> Access Denied!</strong><br><br>
                You have attempted to access a page you are not authorized for.
            </div>
        </div>
    </div>
@endsection