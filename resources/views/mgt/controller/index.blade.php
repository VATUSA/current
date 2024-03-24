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
          url : "{{ url("/mgt/controller/ajax/toggleStaffPrevent") }}",
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
            class="panel panel-{{ (\App\Helpers\AuthHelper::authACL()->canManageRoles() && $user->flag_preventStaffAssign) ? "warning" : "default"}}"
            id="user-info-panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <div class="row">
                        <div class="col-md-8" style="font-size: 16pt;">
                            {{$user->fname}} {{$user->lname}} - {{$user->cid}}
                        </div>
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
                    @if (\App\Helpers\AuthHelper::authACL()->canViewController($user))
                        <li role="presentation"><a href="#ratings" aria-controls="ratings" role="tab"
                                                   data-toggle="tab">Ratings
                                &amp; Transfers</a></li>
                    @endif
                    @php $canViewTraining = \App\Helpers\AuthHelper::authACL()->canViewTrainingRecords($user->facility) @endphp
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
                    @if (\App\Helpers\AuthHelper::authACL()->canUseActionLog())
                        <li role="presentation">
                            <a href="#actions" aria-controls="actions" role="tab" data-toggle="tab">
                                Action Log
                            </a>
                        </li>
                   @endif
                    @if(\App\Helpers\AuthHelper::authACL()->canManageRoles())
                        <li role="presentation">
                            <a href="#roles" data-controls="roles" role="tab" data-toggle="tab">
                                Roles
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="csp"><br>
                        @include('mgt.controller.parts.summary')
                    </div>
                    @if(\App\Helpers\AuthHelper::authACL()->canViewController($user))
                        <div class="tab-pane" role="tabpanel" id="ratings">
                            @include('mgt.controller.parts.rating_transfer')
                        </div>
                    @endif
                    @if($canViewTraining)
                        <div class="tab-pane" role="tabpanel" id="exams">
                            @include('mgt.controller.parts.academy_transcript')
                        </div>
                    @endif
                    <div class="tab-pane" role="tabpanel" id="training">
                        @includeWhen($canViewTraining, 'mgt.controller.training.training')
                    </div>
                    @if(\App\Helpers\AuthHelper::authACL()->canUseActionLog())
                        <div class="tab-pane" role="tabpanel" id="actions">
                            @include('mgt.controller.parts.action_log')
                        </div>
                    @endif
                    @if(\App\Helpers\AuthHelper::authACL()->canManageRoles())
                        <div class="tab-pane" role="tabpanel" id="roles">
                            @include('mgt.controller.parts.roles')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
      function viewXfer (id) {
        $.get("{{url('mgt/ajax/transfer/reason')}}", {id: id}, function (data) {
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
