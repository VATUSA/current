@extends('emails.layout')
@section('title','Tattler Notification')
@section('content')
    Staff member {{$name}} has not visited the website and/or forums for {{$days}} days.
    <br><br>
    As addressed https://www.vatusa.net/forums/index.php?topic=4277.0, every staff member should be checking the forums daily.  This user
    has been flagged because their activity has been at least 30 days from the lowest measured by the website, and the forum.
@endsection