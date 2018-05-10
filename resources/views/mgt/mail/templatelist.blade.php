@extends('mgt.mail.layout')
@section('mailcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Facility Email Templates
            </h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr><th>Template Name</th><th colspan="2">Actions</th></tr>
                </thead>
                <tbody>
                <tr>
                    <td>Exam Assigned</td>
                    <td><a href="/mgt/mail/template/examassigned/edit">Edit</a></td>
                    <td><a href="/mgt/mail/template/examassigned/delete">Use VATUSA</a></td>
                </tr>
                <tr>
                    <td>Exam Passed</td>
                    <td><a href="/mgt/mail/template/exampassed/edit">Edit</a></td>
                    <td><a href="/mgt/mail/template/exampassed/delete">Use VATUSA</a></td>
                </tr>
                <tr>
                    <td>Exam Failed</td>
                    <td><a href="/mgt/mail/template/examfailed/edit">Edit</a></td>
                    <td><a href="/mgt/mail/template/examfailed/delete">Use VATUSA</a></td>
                </tr>
<!--                <tr>
                    <td>Transfer Pending</td>
                    <td><a href="/mgt/mail/template/examassigned/edit">Edit</a></td>
                    <td><a href="/mgt/mail/template/examassigned/delete">Use VATUSA</a></td>
                </tr>-->
                <tr>
                    <td>Welcome Email</td>
                    <td colspan="2"><a href="/mgt/mail/welcome">Edit</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('welcome');
    </script>
@stop