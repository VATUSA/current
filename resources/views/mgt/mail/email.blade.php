@extends('mgt.mail.layout')
@section('mailcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Manage Emails
            </h3>
        </div>
        <div class="panel-body" id="emailRoot">
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{ mix('/js/email.js') }}"></script>
@endsection