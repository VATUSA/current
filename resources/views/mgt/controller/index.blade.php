@extends('layout')
@section('title', 'Controller Management')

@section('scripts')
    <script>
      if (document.location.hash)
        $('.nav-tabs li:not(.disabled) a[href=' + document.location.hash + ']').tab('show')

      $('.nav-tabs a').on('shown.bs.tab', function (e) {
        history.pushState({}, '', e.target.hash)
      })

      $('.delete-log').click(function (e) {
        e.preventDefault()

        let a       = $(this),
            spinner = a.next('i'),
            action  = a.data('action'),
            id      = a.data('id'),
            tr      = $('tr#log-' + id),
            content = tr.find('.log-content').html()
        $('#delete-log-error').hide()
        $('#delete-log-success').hide()
        bootbox.confirm({
          message : '<strong>Are you sure you want to delete the log entry?</strong><hr style="margin-top:10px;margin-bottom:10px;">' + content,
          buttons : {
            confirm: {
              label    : '<i class="fas fa-times"></i> Yes, delete',
              className: 'btn-danger'
            },
            cancel : {
              label    : 'No, go back',
              className: 'btn-default'
            }
          },
          callback: function (result) {
            if (result) {
              spinner.show()
              $.ajax({
                url : action,
                type: 'DELETE'
              }).success(function (result) {
                spinner.hide()
                if (result === '1') {
                  $('#delete-log-success').fadeIn()
                  setTimeout(function () {$('#delete-log-success').fadeOut()}, 3000)
                  tr.remove()
                } else {
                  $('#delete-log-error').fadeIn()
                  setTimeout(function () {$('#delete-log-error').fadeOut()}, 3000)
                }
              }).error(function (result) {
                spinner.hide()
                $('#delete-log-error').fadeIn()
                setTimeout(function () {$('#delete-log-error').fadeOut()}, 3000)
              })
            }
          }
        })

      })

      $('.sub-action-btn').click(function (e) {
        e.preventDefault()
        $(this).attr('disabled', true).html('<i class="spinner-icon fa fa-spinner fa-spin"></i>')
        $(this.form).submit()
      })

      $('#toggleStaffPrevent').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon'),
            panel       = $('#user-info-panel')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleStaffPrevent") }}",
          data: {cid: "{{ $user->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'success' : 'danger'))
            panel.removeClass(currentlyOn ? 'panel-warning' : 'panel-default')
            panel.addClass(currentlyOn ? 'panel-default' : 'panel-warning')
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle prevention of staff assignment setting.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle prevention of staff assignment setting.')
          })
      })

      $('#toggleAcademyEditor').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleAcademyEditor") }}",
          data: {cid: "{{ $user->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'success'))
            panel.removeClass(currentlyOn ? 'panel-warning' : 'panel-default')
            panel.addClass(currentlyOn ? 'panel-default' : 'panel-warning')
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle Academy editor setting.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle prevention of staff assignment setting.')
          })
      })

      $('#toggleAcademyEditorFacility').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleAcademyEditor") }}",
          data: {cid: "{{ $user->cid }}", facOnly: true}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'success'))
            panel.removeClass(currentlyOn ? 'panel-warning' : 'panel-default')
            panel.addClass(currentlyOn ? 'panel-default' : 'panel-warning')
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle Academy editor setting.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle prevention of staff assignment setting.')
          })
      })

      $('#toggleInsRole').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleInsRole") }}",
          data: {cid: "{{ $user->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'success'))
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle instructor role.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle instructor role.')
          })
      })
      $('#toggleSMT').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleSMTRole") }}",
          data: {cid: "{{ $user->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'success'))
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle SMT role.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle SMT role.')
          })
      })

      $('#toggleTT').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/ajax/toggleTTRole") }}",
          data: {cid: "{{ $user->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'success'))
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle VATUSA Tech Team role.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle VATUSA Tech Team role.')
          })
      })

      $('#ratingchange').on('change', function () {
        let hasFlag = $('#toggleStaffPrevent').find('i.toggle-icon').hasClass('fa-toggle-on')
        if (this.value >= parseInt("{{\App\Classes\Helper::ratingIntFromShort("I1")}}") && hasFlag)
          $('#ratingchange-warning').show()
        else $('#ratingchange-warning').hide()
      })
    </script>
@endsection

@section('content')
    <div class="container">
        <div
            class="panel panel-{{ ((\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff()) && $user->flag_preventStaffAssign) ? "warning" : "default"}}"
            id="user-info-panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <div class="row">
                        <div class="col-md-8" style="font-size: 16pt;">{{$user->fname}} {{$user->lname}}
                            - {{$user->cid}}</div>
                        <form class="form-inline" id="controllerForm">
                            <div class="col-md-4 text-right form-group">
                                <input type="text" id="cidsearch" class="form-control" placeholder="CID, Discord ID, Last Name">
                                <button type="button" class="btn btn-primary" id="cidsearchbtn"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </h3>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#csp" aria-controls="csp" role="tab"
                                                              data-toggle="tab">Summary</a></li>
                    @if (\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility)
                        || \App\Classes\RoleHelper::isFacilitySeniorStaff()
                        || \App\Classes\RoleHelper::hasRole(Auth::user()->cid, $user->facility, "WM")
                        || \App\Classes\RoleHelper::isInstructor(Auth::user()->cid, $user->facility))
                        <li role="presentation"><a href="#ratings" aria-controls="ratings" role="tab"
                                                   data-toggle="tab">Ratings
                                &amp; Transfers</a></li>
                    @endif
                    @php $canViewTraining = ($user->facility == Auth::user()->facility
                                            || $user->visits()->where('facility', Auth::user()->facility)->exists()
                                            || $user->trainingRecords()->where('facility_id', Auth::user()->facility)->exists()
                                            || \App\Classes\RoleHelper::isVATUSAStaff()
                                            || \App\Classes\RoleHelper::isFacilitySeniorStaff()) @endphp
                    @if($canViewTraining)
                        <li role="presentation"><a href="#exams" aria-controls="exams" role="tab"
                                                   data-toggle="tab">Academy Transcript</a></li>
                    @endif
                    <li role="presentation" @if(!$canViewTraining) class="disabled" rel="tooltip"
                        title="Not a home or visiting controller at your ARTCC or does not have any records from your ARTCC." @endif>
                        <a href="#training"
                           @if($canViewTraining) data-controls="training"
                           role="tab"
                           data-toggle="tab" @endif>Training</a>
                    </li>
                    <li role="presentation"><a href="#cbt" data-controls="cbt" role="tab"
                                               data-toggle="tab">CBT Progress</a></li>
                    @if (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isInstructor(Auth::user()->cid, $user->facility) || \App\Classes\RoleHelper::hasRole(Auth::user()->cid, $user->facility, "WM"))
                        <li role="presentation"><a href="#actions" aria-controls="actions" role="tab" data-toggle="tab">Action
                                Log</a></li>
                    @endif
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff()
                        || \App\Classes\RoleHelper::isVATUSAStaff()
                        || \App\Classes\RoleHelper::isWebTeam()
                        )
                        <li role="presentation"><a href="#roles" data-controls="roles" role="tab"
                                                   data-toggle="tab">Roles</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="csp"><br>
                        @php $canViewChecks = \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff() @endphp
                        @if($canViewChecks)
                            <div class="row">
                                <div class="col-md-4">@endif
                                    <ol style="font-size: 110%;list-style-type: none;">
                                        <li><strong>{{$user->fname}} {{$user->lname}}</strong></li>
                                        @if(\App\Classes\RoleHelper::isVATUSAStaff() ||
                                            \App\Classes\RoleHelper::isFacilitySeniorStaff())
                                            <li>{{$user->email}} &nbsp; <a href="mailto:{{$user->email}}"><i
                                                        class="fa fa-envelope text-primary"
                                                        style="font-size:80%"></i></a>
                                            </li>
                                        @else
                                            <li>[Email Private] <a href="/mgt/mail/{{$user->cid}}"><i
                                                        class="fa fa-envelope text-primary"></i></a></li>
                                        @endif
                                        <li>
                                            @if($user->flag_broadcastOptedIn)
                                                <p class="text-success"><i class="fa fa-check"></i> Receiving Broadcast
                                                    Emails</p>
                                            @else
                                                <p class="text-danger"><i class="fas fa-times"></i> Not Receiving
                                                    Broadcast
                                                    Emails
                                                </p>
                                            @endif

                                        </li>
                                        <li>{{$user->urating->short}} - {{$user->urating->long}}</li>
                                        <li>Last promoted {{$user->lastPromotion()->created_at ?? 'never.'}}</li>
                                        <br>
                                        <li>{{$user->facility}}
                                            - {{\App\Classes\Helper::facShtLng($user->facility)}}</li>
                                        <li>Member of {{$user->facility}} since {{ $user->facility_join }}</li>
                                        @if(!str_contains($user->urating->long, "Instructor"))
                                            <li>Mentor?
                                                @if(\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $user->facility))
                                                    <a href="/mgt/controller/{{$user->cid}}/mentor">{{(\App\Classes\RoleHelper::isMentor($user->cid))?"Yes":"No"}}</a>
                                                @else
                                                    {{(\App\Classes\RoleHelper::isMentor($user->cid))?"Yes":"No"}}
                                                @endif
                                            </li>
                                        @endif
                                        <br>
                                        @if ($user->visits()->exists())
                                            <li>Visiting:</li>
                                            @foreach ($user->visits()->get() as $visit)
                                                <li>{{$visit->fac->id}} - {{$visit->fac->name}}</li>
                                            @endforeach
                                            <br>
                                        @endif
                                        <li>Last Activity Forum: {{$user->lastActivityForum()}} days ago</li>
                                        <li>Last Activity Website: {{$user->lastActivityWebsite()}} days ago</li>
                                        <br>
                                        <li>Needs Basic ATC Exam:
                                            @if (\App\Classes\RoleHelper::isVATUSAStaff()
                                                || \App\Classes\RoleHelper::isWebTeam())
                                                <a href="/mgt/controller/{{$user->cid}}/togglebasic">
                                            @endif
                                                    @if ($user->flag_needbasic)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                            @if (\App\Classes\RoleHelper::isVATUSAStaff()
                                                || \App\Classes\RoleHelper::isWebTeam())
                                                </a>
                                            @endif
                                        </li>
                                        <br>
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff() &&
                                            $user->rating >= \App\Classes\Helper::ratingIntFromShort("C1") && $user->rating < \App\Classes\Helper::ratingIntFromShort("SUP"))
                                            <li>Rating Change
                                                <select id="ratingchange">
                                                    <option value="{{\App\Classes\Helper::ratingIntFromShort("C1")}}">C1
                                                        -
                                                        Enroute
                                                        Controller
                                                    </option>
                                                    <option value="{{\App\Classes\Helper::ratingIntFromShort("C3")}}">C3
                                                        -
                                                        Senior
                                                        Controller
                                                    </option>
                                                    <option value="{{\App\Classes\Helper::ratingIntFromShort("I1")}}">I1
                                                        -
                                                        Instructor
                                                    </option>
                                                    <option value="{{\App\Classes\Helper::ratingIntFromShort("I3")}}">I3
                                                        -
                                                        Senior
                                                        Instructor
                                                    </option>
                                                </select>
                                                <div class="alert alert-danger" id="ratingchange-warning"
                                                     style="display:none;"><strong><i class="fas fa-times"></i>
                                                        Warning!</strong> This controller currently has the Prevent
                                                    Staff Role Assignment flag.
                                                </div>
                                                <button class="btn btn-info" id="ratingchangebtn">Save</button>
                                                <span class="" id="ratingchangespan"></span></li>
                                            <script type="text/javascript">
                                              $('#ratingchangebtn').click(function () {
                                                $('#ratingchangespan').html('Saving...')
                                                $.ajax({
                                                  url : '/mgt/controller/{{$user->cid}}/rating',
                                                  type: 'POST',
                                                  data: {rating: $('#ratingchange').val()}
                                                }).success(function () {
                                                  $('#ratingchangespan').html('Saved')
                                                  setTimeout(function () {
                                                    $('#ratingchangespan').html('')
                                                  }, 3000)
                                                })
                                                  .error(function () {
                                                    $('#ratingchangespan').html('Error')
                                                    setTimeout(function () {
                                                      $('#ratingchangespan').html('')
                                                    }, 3000)
                                                  })
                                              })
                                            </script>
                                        @endif
                                    </ol>
                                    @if($canViewChecks)</div>
                                <div class="col-md-8" style="border-left: 1px solid #ccc6;">
                                    <table class="table table-responsive table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width:100%;">Transfer eligibility checks</th>
                                            <th>Pass/Fail</th>
                                        </tr>
                                        </thead>
                                        <tr>
                                            <td>In VATUSA division?</td>
                                            <td>{!! ($checks['homecontroller'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Needs to complete the Basic ATC/S1 courses?</td>
                                            <td>{!! ($checks['needbasic'])?'<span class="text-success">No</span>':'<span class="text-danger">Yes</span>' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>90 days since last transfer?</td>
                                            <td>{!! ($checks['90days'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!} {!! (isset($checks['days']))?"(".$checks['days']." days)":"" !!}</td>
                                        </tr>
                                        <tr>
                                            <td>If first facility, within 30 days of joining?</td>
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
                                            <td>Have pending transfers?</td>
                                            <td>{!! ($checks['pending'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Are they eligible?</td>
                                            <td>{!! ($eligible)?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility)
                        || \App\Classes\RoleHelper::isFacilitySeniorStaff()
                        || \App\Classes\RoleHelper::hasRole(Auth::user()->cid, $user->facility, "WM")
                        || \App\Classes\RoleHelper::isInstructor(Auth::user()->cid, $user->facility))
                        <div class="tab-pane" role="tabpanel" id="ratings">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped panel panel-default">
                                        <thead>
                                        <tr style="background:#F5F5F5" class="panel-heading">
                                            <td colspan="4" style="text-align:center"><h3 class="panel-title">Rating
                                                    History</h3>
                                            </td>
                                        </tr>
                                        </thead>
                                        @foreach (\App\Models\Promotions::where('cid', $user->cid)->orderby('created_at', 'desc')->get() as $promo)
                                            <tr style="text-align: center">
                                                <td style="width:30%">{!! $promo->created_at->format('m/d/Y') !!}
                                                    <br><em>{{ \App\Classes\Helper::nameFromCID($promo->grantor) }}</em>
                                                </td>
                                                <td style="vertical-align: middle">
                                                    <strong>{{ \App\Classes\Helper::ratingShortFromInt($promo->from) }}</strong>
                                                </td>
                                                <td style="vertical-align: middle"
                                                    class="{{(($promo->from < $promo->to)? 'text-success' : 'text-danger')}}">
                                                    <i
                                                        class="fa {{(($promo->from < $promo->to) ? 'fa-arrow-up' : 'fa-arrow-down')}}"></i>
                                                </td>
                                                <td style="vertical-align: middle">
                                                    <strong>{{ \App\Classes\Helper::ratingShortFromInt($promo->to) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                @if(!\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility)
                                                    || \App\Classes\RoleHelper::isFacilitySeniorStaff()
                                                    || \App\Classes\RoleHelper::hasRole(Auth::user()->cid, $user->facility, "WM"))
                                    <div class="col-md-6">
                                        <table class="table table-striped panel panel-default">
                                            <thead>
                                            <tr style="background:#F5F5F5" class="panel-heading">
                                                <td colspan="5" style="text-align:center"><h3 class="panel-title">
                                                        Transfer
                                                        History</h3>
                                                </td>
                                            </tr>
                                            </thead>
                                            @foreach(\App\Models\Transfers::where('cid', $user->cid)->orderby('id', 'desc')->get() as $t)
                                                <tr style="text-align: center">
                                                    <td>{{substr($t->updated_at, 0,10)}}</td>
                                                    <td><strong>{{$t->from}}</strong></td>
                                                    <td class="text-{{($t->status == 2 ? 'danger' : ($t->status == 1 ? 'success' : 'warning'))}}">
                                                        <i class="fa fa-arrow-right" data-toggle="tooltip"
                                                           data-original-title="{{($t->status == 2 ? 'Declined - '.$t->actiontext.' by '.\App\Classes\Helper::nameFromCID($t->actionby, 1) : ($t->status == 1 ? 'Approved by '.\App\Classes\Helper::nameFromCID($t->actionby, 1) : 'Pending'))}}"
                                                           style="cursor: pointer"></i></td>
                                                    <td><strong>{{$t->to}}</strong></td>
                                                    <td><a href="#" onClick="viewXfer({{$t->id}})"><i
                                                                class="fa fa-search"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                                <tr>
                                                    <td colspan="5">Transfer Waiver: <span id="waiverToggle"><i
                                                                id="waivertogglei"
                                                                class="fa {{(($user->flag_xferOverride==1) ? "fa-toggle-on text-success" : "fa-toggle-off text-danger")}}"></i></span>
                                                        <a href="/mgt/transfer?cid={{$user->cid}}">Submit TR</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($canViewTraining)
                        <div class="tab-pane" role="tabpanel" id="exams">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        Exam History
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div>
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs nav-justified text-centers" id="exam-tabs"
                                            role="tablist">
                                            <li role="presentation" class="active"><a href="#"
                                                                      data-target="legacy"
                                                                      aria-controls="home"
                                                                      role="tab"
                                                                      data-toggle="tab"
                                                                      class="text-warning">Legacy</a>
                                            </li>
                                            <li role="presentation">
                                                <a
                                                    href="#" data-target="academy"
                                                    aria-controls="academy"
                                                    role="tab" data-toggle="tab"
                                                    class="text-success">Academy</a></li>
                                        </ul>

                                        <!-- Tab panes -->
                                        <div class="tab-content" id="exam-tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="legacy">
                                                <table class="table table-striped">
                                                    @foreach(\App\Models\ExamResults::where('cid',$user->cid)->orderBy('date', 'DESC')->get() as $res)
                                                        <tr style="text-align: center">
                                                            <td style="width:20%">{{substr($res->date, 0, 10)}}</td>
                                                            <td style="width: 70%; text-align: left"><a
                                                                    href="/exam/result/{{$res->id}}">{{$res->exam_name}}</a>
                                                            </td>
                                                            <td{!! ($res->passed)?" style=\"color: green\"":" style=\"color: red\"" !!}>{{$res->score}}
                                                                %
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            <div role="tabpanel"
                                                 class="tab-pane"
                                                 id="academy">
                                                {{-- -<pre>@dump($examAttempts)</pre> --}}
                                                <a href="https://academy.vatusa.net/grade/report/overview/index.php?id=8&userid={{$moodleUid}}"
                                                   style="text-decoration: none"
                                                   target="_blank"><span
                                                            class="label label-success"><i
                                                                class="fas fa-check"
                                                                style="font-size: inherit !important;"></i> View Grades in Academy</span></a>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Exam Name</th>
                                                        <th>Attempts</th>
                                                        <th>Enrollment</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($examAttempts as $exam => $data)
                                                        @php $hasPassed = 0; @endphp
                                                        <tr @if($data['examInfo']['rating'] - 1 > $user->rating) class="text-muted" @endif>
                                                            <td>{{ $exam }}</td>
                                                            <td>@if(empty($data['attempts']) && $data['examInfo']['rating'] - 1 <= $user->rating)
                                                                    <span
                                                                        class="label label-info"><i
                                                                            class="fas fa-question-circle"
                                                                            style="font-size: inherit !important;"></i> Not Taken</span>
                                                                @elseif(empty($data['attempts']))
                                                                    <em>Not Eligible</em>
                                                                @else
                                                                    @foreach($data['attempts'] as $attempt)
                                                                        <p>Attempt
                                                                            <strong>{{ $attempt['attempt'] }}</strong>:
                                                                            @switch($attempt['state'])
                                                                                @case('finished')
                                                                                @if(round($attempt['grade'] >= $data['examInfo']['passingPercent']))
                                                                                    @php $hasPassed = 1; @endphp
                                                                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}"
                                                                                       style="text-decoration: none"
                                                                                       target="_blank"><span
                                                                                            class="label label-success"><i
                                                                                                class="fas fa-check"
                                                                                                style="font-size: inherit !important;"></i> Passed ({{ $attempt['grade'] }}%)</span></a>
                                                                                @else
                                                                                    <a href="https://academy.vatusa.net/mod/quiz/review.php?attempt={{$attempt['id']}}"
                                                                                       style="text-decoration: none"
                                                                                       target="_blank"><span
                                                                                            class="label label-danger"><i
                                                                                                class="fas fa-times"
                                                                                                style="font-size: inherit !important;"></i> Failed ({{ $attempt['grade'] }}%)</span></a>
                                                                                @endif
                                                                                @break
                                                                                @case('inprogress')
                                                                                <span class="label label-warning"><i
                                                                                        class="fas fa-clock"
                                                                                        style="font-size: inherit !important;"></i> In Progress</span>
                                                                                @break
                                                                                @default
                                                                                <span
                                                                                    class="label label-danger">{{ ucwords($attempt['state']) }}</span>
                                                                                @break
                                                                            @endswitch
                                                                            <br>
                                                                        </p>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td @if($data['examInfo']['id'] != config('exams.BASIC.id')) id="enrollment-status-{{ $data['examInfo']['courseId'] }}" @endif>
                                                                @if($hasPassed)
                                                                    <strong style="color: #39683a"><em><i
                                                                                class="fas fa-check-double"></i> Course
                                                                            Complete</em></strong>
                                                                @elseif ($data['assignDate'])
                                                                    <strong class="text-success"><i
                                                                            class="fas fa-user-check"></i>
                                                                        Enrolled</strong>
                                                                    on
                                                                    {{ $data['assignDate'] }}
                                                                @elseif($data['examInfo']['id'] == config('exams.BASIC.id') || $data['examInfo']['rating'] <= $user->rating)
                                                                    <em>Auto-Enrolled</em>
                                                                @elseif($data['examInfo']['rating'] - 1 == $user->rating)
                                                                    @if(\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility) && $data['examInfo']['rating'] <= Auth::user()->rating ||
                                                                          !\App\Classes\RoleHelper::isMentor(Auth::user()->cid, $user->facility))
                                                                        <button
                                                                            class="btn btn-success btn-sm enrol-exam-course"
                                                                            data-id="{{ $data['examInfo']['courseId'] }}"
                                                                            data-name="{{ $exam }}">
                                                                            <i
                                                                                class="fas fa-user-plus"></i> Enroll
                                                                        </button>
                                                                    @else
                                                                        <span
                                                                            class="label label-danger"><i
                                                                                class="fas fa-times-circle"
                                                                                style="font-size: inherit !important;"></i> Not Enrolled</span>
                                                                    @endif
                                                                @else
                                                                    <em>Not Eligible</em>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="tab-pane" role="tabpanel"
                         id="training">@includeWhen($canViewTraining, 'mgt.controller.training.training')</div>
                    <div class="tab-pane" role="tabpanel" id="cbt">
                        <h3>CBT Progress</h3>
                        <div class="panel-group" id="accordion">
                            @foreach(\App\Models\TrainingBlock::where('visible', 1)->orderBy('facility')->orderBy('order')->get() as $block)
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <a href="#collapse{{$block->id}}" data-parent="#accordion"
                                               data-toggle="collapse">({{$block->facility}}) {{$block->name}}</a>
                                        </h3>
                                    </div>
                                    <div id="collapse{{$block->id}}" class="panel-collapse collapse">
                                        <table class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th style="width: auto;">Chapter</th>
                                                <th style="width: 100px;">Complete</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($block->chapters as $chapter)
                                                <tr>
                                                    <td>{{$chapter->name}}</td>
                                                    <td>
                                                        @if(\App\Classes\CBTHelper::isComplete($chapter->id, $user->cid))
                                                            <i class="text-success fa fa-check"></i>
                                                        @else
                                                            <i class="text-danger fa fa-times"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if (\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isInstructor(Auth::user()->cid, $user->facility) || \App\Classes\RoleHelper::hasRole(Auth::user()->cid, $user->facility, "WM"))
                        <div class="tab-pane" role="tabpanel" id="actions">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        Action Log
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                        <div class="alert alert-success" id="delete-log-success" style="display:none;">
                                            <strong><i class='fa fa-check'></i> Success! </strong> The log entry has
                                            been
                                            deleted.
                                        </div>
                                        <div class="alert alert-danger" id="delete-log-error" style="display:none;">
                                            <strong><i
                                                    class='fa fa-check'></i> Error! </strong> Could not delete log
                                            entry.
                                        </div>
                                    @endif
                                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff())
                                        <form class="form-horizontal"
                                              action="{{secure_url("/mgt/controller/action/add")}}"
                                              method="POST">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="to" value="{{ $user->cid }}">
                                            <input type="hidden" name="from" value="{{ Auth::user()->cid }}">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Add a Log Entry</label>
                                                <div class="col-sm-10"><textarea class="form-control" rows="2"
                                                                                 name="log"
                                                                                 placeholder="Entry"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" class="btn btn-success sub-action-btn"
                                                            value="submit">
                                                        <i class="fa fa-check"></i> Submit
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <hr>
                                    @endif
                                    <table class="table table-striped">
                                        @foreach(\App\Models\Actions::where('to', $user->cid)->orderby('id', 'desc')->get() as $a)
                                            <tr id="log-{{ $a->id }}">
                                                <td style="width:10%"><strong>{{substr($a->created_at, 0,10)}}</strong>
                                                </td>
                                                <td class="log-content">{{$a->log}}
                                                    @php $name = \App\Classes\Helper::nameFromCID($a->from) @endphp
                                                    @if($a->from && !str_contains($a->log, "by $name"))
                                                        <p class="help-block">Added
                                                            by {{ $name }}</p>
                                                    @endif</td>
                                                <td>
                                                    @if(App\Classes\RoleHelper::isVATUSAStaff() && $a->from &&
                                                    !str_contains($a->log, 'by ' . App\Classes\Helper::nameFromCID($a->from)))
                                                        <a data-id="{{ $a->id }}"
                                                           href="#"
                                                           data-action="{{ secure_url('mgt/controller/action/delete/'.$a->id) }}"
                                                           class="text-danger delete-log"><i
                                                                class="fa fa-times"></i></a>
                                                        <i class="spinner-icon fa fa-spinner fa-spin"
                                                           style="display:none;"></i>

                                                    @else &nbsp;
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff()
                        || \App\Classes\RoleHelper::isVATUSAStaff()
                        || \App\Classes\RoleHelper::isWebTeam()
                        )
                        <div class="tab-pane" role="tabpanel" id="roles">
                            @if(\App\Classes\RoleHelper::isVATUSAStaff($user->cid))
                                <div class="alert alert-info"><i class="fas fa-info-circle"></i> This user has all roles
                                    as VATUSA Staff.
                                </div>
                            @else
                                @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Prevent Staff Role Assignment</label>
                                        <div class="col-sm-10">
                                    <span id="toggleStaffPrevent" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ $user->flag_preventStaffAssign ? "on text-danger" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                            <p class="help-block">This will prevent the controller from being assigned a
                                                staff role by facility staff. <br> Only a VATUSA Staff Member will be
                                                able
                                                to
                                                assign him or her a role.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Academy Material Editor</label>
                                        <div class="col-sm-10">
                                    <span id="toggleAcademyEditor" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::hasRole($user->cid, "ZAE", "CBT") ? "on text-success" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                            <p class="help-block">This will assign the Editor role to the user in
                                                Moodle,
                                                and will allow him or her to edit VATUSA Training Academy material.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Social Media Team</label>
                                        <div class="col-sm-10">
                                    <span id="toggleSMT" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::hasRole($user->cid, "ZHQ", "SMT") ? "on text-success" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                            <p class="help-block">This will allow the user to receive the proper roles
                                                in Discord.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">VATUSA Tech Team</label>
                                        <div class="col-sm-10">
                                    <span id="toggleTT" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::hasRole($user->cid, "ZHQ", "USWT") ? "on text-success" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                            <p class="help-block">This will allow the user to receive the proper roles
                                                in Discord.</p>
                                        </div>
                                    </div>
                                    @if($user->rating == \App\Classes\Helper::ratingIntFromShort("SUP"))
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Instructor</label>
                                            <div class="col-sm-10">
                                        <span id="toggleInsRole" style="font-size:1.8em;">
                                            <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::isInstructor($user->cid, $user->facility, false) ? "on text-success" : "off text-danger"}} "></i>
                                            <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                        </span>
                                                <p class="help-block">This will grant the supervisor Instructor
                                                    privileges.</p>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Prevent Staff Role Assignment</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static" style="cursor:default;">
                                                @if($user->flag_preventStaffAssign) <strong
                                                    style="color:#e72828">Yes</strong>
                                                @else <strong style="color:green">No</strong>
                                                @endif
                                            </p>
                                            <p class="help-block">This will prevent the controller from being assigned a
                                                staff role by facility staff. <br>Only a VATUSA Staff Member will be
                                                able to
                                                assign him or her a role.</p>
                                        </div>
                                    </div>
                                    @if($user->rating == \App\Classes\Helper::ratingIntFromShort("SUP"))
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Instructor</label>
                                            <div class="col-sm-10">
                                                <p class="form-control-static" style="cursor:default;">
                                                    @if(\App\Classes\RoleHelper::isInstructor($user->cid, $user->facility, false))
                                                        <strong style="color:green">Yes</strong>
                                                    @else <strong style="color:#e72828">No</strong>
                                                    @endif
                                                </p>
                                                <p class="help-block">This will grant the supervisor Instructor
                                                    privileges.</p>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                @if(!\App\Classes\RoleHelper::isFacilitySeniorStaff($user->cid, $user->facility, true))
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Academy Material Editor (Facility)</label>
                                        <div class="col-sm-10">
                                    <span id="toggleAcademyEditorFacility" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::hasRole($user->cid, $user->facility, "FACCBT") ? "on text-danger" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                            <p class="help-block">This will assign the Editor role to the user in
                                                Moodle,
                                                and will allow him or her to edit the facility Moodle material.</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
      function viewXfer (id) {
        $.get("{{secure_url('mgt/ajax/transfer/reason')}}", {id: id}, function (data) {
          bootbox.alert(data)
        })
      }

      $('#waiverToggle').click(function () {
        $.ajax({
          url : '/mgt/controller/{{$user->cid}}/transferwaiver',
          type: 'GET'
        }).success(function (data) {
          if (data == '1') {
            $('#waivertogglei').attr('class', 'fa fa-toggle-on text-success')
          } else {
            $('#waivertogglei').attr('class', 'fa fa-toggle-off text-danger')
          }
        })
      })
      $('#cidsearchbtn').click(function () {
        var cid = $('#cidsearch').val()
        cid = cid.replace(/\s+/g, '')
        $('#cidsearch').val(cid)

        if (isNaN($('#cidsearch').val())) {
          bootbox.alert('CID must be numbers only')
          return
        }
        window.location = '/mgt/controller/' + cid
      })
      $('#cidsearch').keyup(function (e) {
        if (e.keyCode == 13) {
          $('#cidsearchbtn').click()
          return false
        }
      })

      $(document).on('click', '.panel-heading span.clickable', function (e) {
        if (!$(this).hasClass('panel-collapsed')) {
          $(this).parents('.panel').find('.panel-body').slideUp()
          $(this).addClass('panel-collapsed')
          $(this).find('i').removeClass('glyphicon-chevon-up').addClass('glyphicon-chevron-down')
        } else {
          $(this).parents('.panel').find('.panel-body').slideDown()
          $(this).removeClass('panel-collapsed')
          $(this).find('i').addClass('glyphicon-chevon-down').addClass('glyphicon-chevron-up')
        }
      })

      $('#cidsearch').devbridgeAutocomplete({
        lookup  : [],
        onSelect: (suggestion) => {
          $('#cidsearch').val(suggestion.data)
          $('#cidsearchbtn').click()
        }
      })
      var prevVal = ''
      $('#cidsearch').on('change keydown keyup paste', function () {
        let newVal = $(this).val()
        if (newVal.length === 4 && newVal !== prevVal) {
          let url = '/v2/user/' + (isNaN(newVal) ? 'filterlname/' : 'filtercid/')
          prevVal = newVal
          $.get($.apiUrl() + url + newVal)
            .success((data) => {
              $('#cidsearch').devbridgeAutocomplete().setOptions({
                lookup: $.map(data.data, (item) => {
                  return {value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid}
                })
              })
              $('#cidsearch').focus()
            })
        }
      })

      $('.enrol-exam-course').click(function () {
        let btn  = $(this),
            id   = btn.data('id'),
            name = btn.data('name')
        swal({
          title  : 'Enrolling User in Rating Course',
          text   : 'Are you sure you want to enrol this controller in the ' + name + ' course?',
          icon   : 'warning',
          buttons: {
            cancel : {
              text   : 'No, cancel',
              visible: true,
            },
            confirm: {
              text      : 'Yes, enroll',
              className : 'btn-success',
              closeModal: false
            }
          }
        })
          .then(resp => {
            if (resp) {
              $.ajax({
                url   : $.apiUrl() + '/v2/academy/enroll/' + id,
                data  : {cid: {{ $user->cid }}},
                method: 'POST'
              }).success(data => {
                if (data.data.status === 'OK') {
                  swal('Success!', 'The controller has been enrolled in the course.', 'success')
                  $('#enrollment-status-' + id).html('<strong class="text-success"><i class="fas fa-user-check"></i> Enrolled</strong> on ' + moment().utc().format('YYYY-MM-DD HH:MM'))
                } else
                  swal('Error!', 'Unable to enroll the controller in the course. Please try again later.', 'success')
              })
                .error(err => swal('Error!', 'Unable to enroll the controller in the course. ' + JSON.stringify(err), 'error'))
            } else {
              return false
            }
          })
      })

      $('#exam-tabs a').on('click', function (e) {
        e.preventDefault()

        $('#exam-tab-content .tab-pane').hide()
        $('#' + $(this).data('target')).show()
      })
    </script>


@endsection
