@extends('layout')
@section('title', 'My Profile')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <p>Press the button to be assigned the survey.</p>
                <button class="btn btn-primary assignSurvey">Assign Survey</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).on('click','.assignSurvey', () => {
          waitingDialog.show();
          $.ajax({
            method: 'post',
            url: 'https://api.vatusa.net/v2/survey/1/assign/{{ \Auth::user()->cid }}'
          }).done((e) => {
            waitingDialog.hide();
            bootbox.alert("Survey has been assigned. Please check your email.");
          }).fail((r) => {
            bootbox.alert("An error occurred, status code: " + r.status);
          });
        });
    </script>
@stop
