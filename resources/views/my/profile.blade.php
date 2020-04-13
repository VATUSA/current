@extends('layout')
@section('title', 'My Profile')

@section('scripts')
    <script>
      $('#toggleOptin').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/my/profile/toggleBroadcast") }}"
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'danger' : 'success'))
          }
          else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle email opt-in setting.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle email opt-in setting.')
          })
      })
    </script>
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    My Profile
                </h3>
            </div>
            <div class="panel-body">
                @if(session('fromAgreed'))
                    <div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Please review your email broadcast opt-in setting.</strong></div>
                    @php session()->forget('fromAgreed') @endphp
                @endif
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{Auth::user()->fname}} {{Auth::user()->lname}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{Auth::user()->email}}</p>
                            <span id="helpBlock" class="help-block">Click <a
                                    href="http://cert.vatsim.net/vatsimnet/newmail.php">here</a> to change. Your email will be updated here on your next login.</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Facility</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{\App\Classes\Helper::facShtLng(Auth::user()->facility)}}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Rating</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{Auth::user()->urating->short}}
                                ({{Auth::user()->urating->long}})</p>
                            <p class="help-block">Your rating is updated on login. If it is incorrect, <a href="https://login.vatusa.net?logout">logout</a> and log back in.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Receive Broadcast Emails</label>
                        <div class="col-sm-10">
                            <span id="toggleOptin" style="font-size:1.8em;">
                                <i class="toggle-icon fa fa-toggle-{{ Auth::user()->flag_broadcastOptedIn ? "on text-success" : "off text-danger"}} "></i>
                                <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                            </span>
                            <p class="help-block">To receive emails from the VATUSA mass emailing system, you must
                                opt-in by
                                clicking on the toggle switch above. <br>This only affects the mass emailing system of
                                ARTCCs
                                that choose to use this response.<br><strong>This setting does not affect
                                    account-related emails like transfer requests and exam results/assignments.</strong>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
            <table class="table table-responsive table-striped">
                <thead>
                <tr>
                    <th style="width:100%;">Transfer eligibility checks</th>
                    <th>Pass/Fail</th>
                </tr>
                </thead>
                <tr>
                    <td>Is in VATUSA Division?</td>
                    <td>{!! ($checks['homecontroller'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>Do you need the Basic ATC Exam?</td>
                    <td>{!! ($checks['needbasic'])?'<span class="text-success">No</span>':'<span class="text-danger">Yes, <a href="/my/assignbasic">Request Exam</a></span>' !!}</td>
                </tr>
                <tr>
                    <td>Has it been at least 90 days since your last transfer?</td>
                    <td>{!! ($checks['90days'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>If it's your first facility, are you within 30 days of joining?</td>
                    @if($checks['is_first'] == 0)
                        <td><span class="text-success">N/A</span></td>
                    @elseif($checks['initial'] == 1)
                        <td><i class="fa fa-check text-success"></i></td>
                    @else
                        <td><i class="fa fa-times text-danger"></i></td>
                    @endif
                </tr>
                <tr>
                    <td>Has it been at least 90 days since promotion to S1, S2 or S3?</td>
                    <td>{!! ($checks['promo'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>Does not hold a staff position at a facility?</td>
                    <td>{!! ($checks['staff'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>Does not hold an I1 or I3 rating?</td>
                    <td>{!! ($checks['instructor'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>Do you have pending transfers?</td>
                    <td>{!! ($checks['pending'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
                <tr>
                    <td>If the above are all green, you are eligible to submit a transfer request. Are you eligible?
                    </td>
                    <td>{!! ($eligible)?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                </tr>
            </table>
        </div>
    </div>
@stop
