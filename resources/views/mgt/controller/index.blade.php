@extends('layout')
@section('title', 'Controller Management')

@section('scripts')
    <script>
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
              label    : '<i class="fa fa-remove"></i> Yes, delete',
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
          url : "{{ secure_url("/mgt/controller/toggleStaffPrevent") }}",
          data: {cid: "{{ $u->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'info' : 'danger'))
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

      $('#toggleInsRole').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on'),
            spinner     = $(this).find('i.spinner-icon')

        spinner.show()
        $.ajax({
          type: 'POST',
          url : "{{ secure_url("/mgt/controller/toggleInsRole") }}",
          data: {cid: "{{ $u->cid }}"}
        }).success(function (result) {
          spinner.hide()
          if (result === '1') {
            //Success
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'danger' : 'success'))
          } else {
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle instructor role.')
          }
        })
          .error(function (result) {
            spinner.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><i class=\'fa fa-warning\'></i> <strong>Error!</strong> Unable to toggle instructor role.')
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
            class="panel panel-{{ ((\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff()) && $u->flag_preventStaffAssign) ? "warning" : "default"}}"
            id="user-info-panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <div class="row">
                        <div class="col-md-8" style="font-size: 16pt;">{{$u->fname}} {{$u->lname}} - {{$u->cid}}</div>
                        <form class="form-inline">
                            <div class="col-md-4 text-right form-group">
                                <input type="text" id="cidsearch" class="form-control" placeholder="CID">
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
                    @if (!\App\Classes\RoleHelper::isMentor() || (\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor()))
                        <li role="presentation"><a href="#ratings" aria-controls="ratings" role="tab" data-toggle="tab">Ratings
                                &amp; Transfers</a></li>
                        <li role="presentation"><a href="#exams" aria-controls="exams" role="tab"
                                                   data-toggle="tab">Exams</a></li>
                    @endif
                    <li role="presentation"><a href="#training" data-controls="training" role="tab"
                                               data-toggle="tab">Training</a></li>
                    <li role="presentation"><a href="#cbt" data-controls="cbt" role="tab"
                                               data-toggle="tab">CBT Progress</a></li>
                    @if (!\App\Classes\RoleHelper::isMentor() || (\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor()))
                        <li role="presentation"><a href="#actions" aria-controls="actions" role="tab" data-toggle="tab">Action
                                Log</a></li>
                        <li role="presentation"><a href="#tickets" aria-controls="tickets" role="tab" data-toggle="tab">Support
                                Tickets</a></li>
                    @endif
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
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
                                        <li><strong>{{$u->fname}} {{$u->lname}}</strong></li>
                                        @if(\App\Classes\RoleHelper::isVATUSAStaff() ||
                                            \App\Classes\RoleHelper::isFacilitySeniorStaff())
                                            <li>{{$u->email}} &nbsp; <a href="mailto:{{$u->email}}"><i
                                                        class="fa fa-envelope text-primary"
                                                        style="font-size:80%"></i></a>
                                            </li>
                                        @else
                                            <li>[Email Private] <a href="/mgt/mail/{{$u->cid}}"><i
                                                        class="fa fa-envelope text-primary"></i></a></li>
                                        @endif
                                        <li>
                                            @if($u->flag_broadcastOptedIn)
                                                <p class="text-success"><i class="fa fa-check"></i> Receiving Broadcast
                                                    Emails</p>
                                            @else
                                                <p class="text-danger"><i class="fa fa-remove"></i> Not Receiving
                                                    Broadcast
                                                    Emails
                                                </p>
                                            @endif

                                        </li>
                                        <li>{{$u->urating->short}} - {{$u->urating->long}}</li>
                                        <li>{{$u->facility}}
                                            - {{\App\Classes\Helper::facShtLng($u->facility)}}</li>
                                        <li>Member of {{$u->facility}} since {{ $u->facility_join }}</li>
                                        @if(!str_contains($u->urating->long, "Instructor"))
                                            <li>Mentor?
                                                @if(\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $u->facility))
                                                    <a href="/mgt/controller/{{$u->cid}}/mentor">{{(\App\Classes\RoleHelper::isMentor($u->cid))?"Yes":"No"}}</a>
                                                @else
                                                    {{(\App\Classes\RoleHelper::isMentor($u->cid))?"Yes":"No"}}
                                                @endif
                                            </li>
                                        @endif
                                        <br>
                                        <li>Last promoted {{ $u->lastPromotion() }}</li>
                                        <br>
                                        <li>Last Activity Forum: {{$u->lastActivityForum()}} days ago</li>
                                        <li>Last Activity Website: {{$u->lastActivityWebsite()}} days ago</li>
                                        <br>
                                        <li>Needs Basic ATC Exam?
                                            @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                                <a href="/mgt/controller/{{$u->cid}}/togglebasic">
                                                    @endif
                                                    @if ($u->flag_needbasic)
                                                        Yes
                                                    @else
                                                        No
                                                    @endif
                                                    @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                                </a>
                                            @endif
                                        </li>
                                        <br>
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff() &&
                                            $u->rating >= \App\Classes\Helper::ratingIntFromShort("C1") && $u->rating < \App\Classes\Helper::ratingIntFromShort("SUP"))
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
                                                     style="display:none;"><strong><i class="fa fa-warning"></i>
                                                        Warning!</strong> This controller currently has the Prevent
                                                    Staff Role Assignment flag.
                                                </div>
                                                <button class="btn btn-info" id="ratingchangebtn">Save</button>
                                                <span class="" id="ratingchangespan"></span></li>
                                            <script type="text/javascript">
                                              $('#ratingchangebtn').click(function () {
                                                $('#ratingchangespan').html('Saving...')
                                                $.ajax({
                                                  url : '/mgt/controller/{{$u->cid}}/rating',
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
                                            <td>Is in VATUSA division?</td>
                                            <td>{!! ($checks['homecontroller'])?'<i class="fa fa-check text-success"></i>':'<i class="fa fa-times text-danger"></i>' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>Need the Basic ATC Exam?</td>
                                            <td>{!! ($checks['needbasic'])?'<span class="text-success">No</span>':'<span class="text-danger">Yes, <a href="/my/assignbasic">Request Exam</a></span>' !!}</td>
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
                    @if (!\App\Classes\RoleHelper::isMentor() || (\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor()))
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
                                        @foreach (\App\Promotions::where('cid', $u->cid)->orderby('created_at', 'desc')->get() as $promo)
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
                                                        class="fa {{(($promo->from < $promo->to)? 'fa-arrow-up' : 'fa-arrow-down')}}"></i>
                                                </td>
                                                <td style="vertical-align: middle">
                                                    <strong>{{ \App\Classes\Helper::ratingShortFromInt($promo->to) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped panel panel-default">
                                        <thead>
                                        <tr style="background:#F5F5F5" class="panel-heading">
                                            <td colspan="5" style="text-align:center"><h3 class="panel-title">Transfer
                                                    History</h3>
                                            </td>
                                        </tr>
                                        </thead>
                                        @foreach(\App\Transfers::where('cid', $u->cid)->orderby('id', 'desc')->get() as $t)
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
                                                            class="fa {{(($u->flag_xferOverride==1)?"fa-toggle-on text-success":"fa-toggle-off text-danger")}}"></i></span>
                                                    <a href="/mgt/err?cid={{$u->cid}}">Submit TR</a>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" role="tabpanel" id="exams">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        Exam History
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        @foreach(\App\ExamResults::where('cid',$u->cid)->orderBy('date', 'DESC')->get() as $res)
                                            <tr style="text-align: center">
                                                <td style="width:20%">{{substr($res->date, 0, 10)}}</td>
                                                <td style="width: 70%; text-align: left"><a
                                                        href="/exam/result/{{$res->id}}">{{$res->exam_name}}</a></td>
                                                <td{!! ($res->passed)?" style=\"color: green\"":" style=\"color: red\"" !!}>{{$res->score}}
                                                    %
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="tab-pane" role="tabpanel" id="training">Coming soon</div>
                    <div class="tab-pane" role="tabpanel" id="cbt">
                        <h3>CBT Results</h3>
                        <div class="panel-group" id="accordion">
                            @foreach(\App\TrainingBlock::where('visible',1)->orderBy('facility')->orderBy('order')->get() as $block)
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
                                                <th style="width: 100px;">Compl</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($block->chapters as $chapter)
                                                <tr>
                                                    <td>{{$chapter->name}}</td>
                                                    <td>
                                                        @if(\App\Classes\CBTHelper::isComplete($chapter->id, $u->cid))
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
                    @if (!\App\Classes\RoleHelper::isMentor() || (\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor()))
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
                                    <form class="form-horizontal" action="{{secure_url("/mgt/action/add")}}"
                                          method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="to" value="{{ $u->cid }}">
                                        <input type="hidden" name="from" value="{{ Auth::user()->cid }}">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Add a Log Entry</label>
                                            <div class="col-sm-10"><textarea class="form-control" rows="2" name="log"
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
                                    <table class="table table-striped">
                                        @foreach(\App\Actions::where('to', $u->cid)->orderby('id', 'desc')->get() as $a)
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
                                                           data-action="{{ secure_url('mgt/deleteActionLog/'.$a->id) }}"
                                                           class="text-danger delete-log"><i
                                                                class="fa fa-remove"></i></a>
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
                        <div class="tab-pane" role="tabpanel" id="tickets">Coming soon</div>
                    @endif
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
                        <div class="tab-pane" role="tabpanel" id="roles">
                            @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Prevent Staff Role Assignment</label>
                                    <div class="col-sm-10">
                                    <span id="toggleStaffPrevent" style="font-size:1.8em;">
                                        <i class="toggle-icon fa fa-toggle-{{ $u->flag_preventStaffAssign ? "on text-danger" : "off text-info"}} "></i>
                                        <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                                    </span>
                                        <p class="help-block">This will prevent the controller from being assigned a
                                            staff role by facility staff. <br> Only a VATUSA Staff Member will be able
                                            to
                                            assign him or her a role.</p>
                                    </div>
                                </div>
                                @if($u->rating == \App\Classes\Helper::ratingIntFromShort("SUP"))
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Instructor</label>
                                        <div class="col-sm-10">
                                        <span id="toggleInsRole" style="font-size:1.8em;">
                                            <i class="toggle-icon fa fa-toggle-{{ \App\Classes\RoleHelper::isInstructor($u->cid, $u->facility) ? "on text-success" : "off text-danger"}} "></i>
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
                                            @if($u->flag_preventStaffAssign) <strong style="color:#e72828">Yes</strong>
                                            @else <strong style="color:green">No</strong>
                                            @endif
                                        </p>
                                        <p class="help-block">This will prevent the controller from being assigned a
                                            staff role by facility staff. <br>Only a VATUSA Staff Member will be able to
                                            assign him or her a role.</p>
                                    </div>
                                </div>
                                @if($u->rating == \App\Classes\Helper::ratingIntFromShort("SUP"))
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Instructor</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static" style="cursor:default;">
                                                @if(\App\Role::where("facility", $u->facility)
                                                    ->where("cid", $u->cid)->where("role", "INS")->count())
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
          url : '/mgt/controller/{{$u->cid}}/transferwaiver',
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
    </script>


@endsection
