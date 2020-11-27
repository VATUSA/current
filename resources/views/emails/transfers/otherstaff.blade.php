@extends('emails.layout')
@section('title','Transfer Discrepency')
@section('content')
    <p>Dear ATM/DATM,</p>

    <p>This is a notification of a possible descrepancy. User {{$user->fullname()}} ({{$user->cid}}
        /{{\App\Classes\Helper::ratingShortFromInt($user->rating)}})
        has been added to facility {{$user->facility}}. However, our code detected that the user also holds a
        staff position at {{$oldfac->id}}.</p>

    <p>Please verify whether this is accurate or not.</p>
@endsection