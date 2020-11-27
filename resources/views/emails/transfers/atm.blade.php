@extends('emails.layout')
@section('title','Transfer Discrepency')
@section('content')
    <p>This is a notification of a possible descrepancy. User {{$user->fullname()}} ({{$user->cid}}
        /{{\App\Classes\Helper::ratingShortFromInt($user->rating)}})
        has been added to facility {{$user->facility}}. However, the user also holds an ATM or
        DATM staff position at {{$oldfac->id}}.</p>

    <p>Please verify whether this is accurate or not. </p>
@endsection