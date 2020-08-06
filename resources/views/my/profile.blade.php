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
          } else {
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
                    <div class="alert alert-info"><strong><i class="fas fa-info-circle"></i> Please review your email
                            broadcast opt-in setting.</strong></div>
                    @php session()->forget('fromAgreed') @endphp
                @elseif(session()->exists('discordError'))
                    @if(session()->pull('discordError'))
                        <div class="alert alert-danger"><strong><i class="fas fa-times"></i> Unable to link Discord
                                account. Please try again later.</strong></div>
                    @else
                        <div class="alert alert-success"><strong><i class="fas fa-check"></i> Discord has been
                                successfully linked.</strong></div>
                    @endif
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
                            <p class="help-block">Your rating is updated on login. If it is incorrect, <a
                                    href="https://login.vatusa.net?logout">logout</a> and log back in.</p>
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
                                clicking on the toggle switch above. <br>This only affects the mass emailing system
                                of
                                ARTCCs
                                that choose to use this response.<br><strong>This setting does not affect
                                    account-related emails like transfer requests and exam
                                    results/assignments.</strong>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><i class="fab fa-discord"></i> VATUSA Discord
                            Link</label>
                        <div class="col-sm-10">
                            <div class="btn btn-group" style="padding: 0; margin-top: 5px;">
                                @if(!Auth::user()->discord_id)
                                    <button class="btn btn-success" type="button" id="link-discord"
                                            onclick="window.location='{{ secure_url('my/discord/link') }}'">
                                        <i class="fas fa-link"></i> Link
                                        Accounts
                                    </button>@else
                                    <button class="btn btn-success" disabled><i class="fas fa-check"></i> Linked
                                        <button class="btn btn-info" id="assign-roles" data-loading-text="Assigning...">
                                            <i
                                                class="fas fa-sync-alt"></i> Assign Roles
                                        </button>
                                        <button class="btn btn-danger" id="unlink" data-loading-text="Unlinking..."><i
                                                class="fas fa-unlink"></i>
                                            Unlink
                                        </button>
                                    </button>@endif
                            </div>
                            <p class="help-block"><strong>Click <a href="https://discord.gg/a7Qcse7"
                                                                   target="_blank">here</a> to
                                    join the Discord. <br></strong>Once joined and linked, you may
                                assign your roles by clicking "Assign Roles."<br>To automatically synchronize your
                                rating, register with <a href="https://vatsimsync.com"
                                                         target="_blank">VATSIMSync</a>.
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
                    <td>Has it been at least 90 days since promotion to S1, S2, S3, or C1?</td>
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
@push('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
      $(function () {
        $('#unlink').click(function (e) {
          e.preventDefault()
          let btn = $(this).button('loading')
          swal({
            title     : 'Unlinking Discord Account',
            text      : 'Are you sure you want to unlink your Discord account?',
            icon      : 'warning',
            buttons   : true,
            dangerMode: true,
            closeModal: false
          })
            .then((willDelete) => {
                btn.button('reset')
                if (willDelete) {
                  $.get('/my/discord/unlink', result => {
                    if (result) swal('Success!', 'Your discord has been successfully unlinked.', 'success').then(() => {window.location.reload()})
                    else swal('Error!', 'Unable to unlink discord. Please try again later.', 'error')
                  }).fail(() => {
                    swal('Error!', 'Unable to unlink discord. Please try again later.', 'error')
                  })
                }
              }
            )
        })
        $('#assign-roles').click(function (e) {
          e.preventDefault()
          let btn = $(this).button('loading')
          $.post("{{ config('services.discord.botserver') . "/assignRoles/" . Auth::user()->discord_id}}", result => {
            btn.button('reset')
            if (result.hasOwnProperty('status') && result.hasOwnProperty('msg') && result.status === 'OK') {
              let content = document.createElement('p')
              content.innerHTML = result.msg
              return swal({
                title  : 'Success!',
                content: content,
                icon   : 'success'
              })
            }

            if (result.msg.match(/^You are not a member/)) {
              return swal({
                title  : 'Error!',
                text   : result.msg,
                icon   : 'error',
                buttons: {
                  join: {
                    text     : 'Join Discord',
                    value    : 'join',
                    className: 'btn-success'
                  },
                  ok  : {
                    text: 'OK'
                  }
                }
              }).then(selection => {
                switch (selection) {
                  case 'join':
                    window.open('https://discord.gg/a7Qcse7', '_blank')
                    break
                  default:
                    return
                }
              })
            }
            return swal('Error!', result.msg, 'error')
          }).fail(result => {
            btn.button('reset')
            return swal('Error!', 'Unable to assign roles. Please try again later.', 'error')
          })
        })
      })
    </script>
@endpush