@extends('layout')
@section('title', 'TMU Map Management')

@push('scripts')
    <link type="text/css" href="{{ asset('datetimepicker/datetimepicker.css') }}" rel="stylesheet">
    <script src="{{ asset('datetimepicker/datetimepicker.js') }}"></script>
    <!--<script
        src="https://cdn.tiny.cloud/1/el8ylh3j522wfpdqh9jom4690z2k11t6m4cpz6kno4vn54oa/tinymce/5/tinymce.min.js"></script>-->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
@endpush

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(\App\Classes\RoleHelper::isVATUSAStaff())
                        <select id="fac" class="mgt-sel">
                            @foreach(\App\Facility::where('active', 1)->orderBy('name')->get() as $f)
                                <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                            @endforeach
                        </select>&nbsp;-&nbsp;
                    @endif
                    {{$facname}} TMU Map Management
                </h3>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active"><a href="#facilities" aria-controls="facilities" role="tab"
                                                              data-toggle="tab">Facilities</a></li>
                    <li role="presentation"><a href="#notices" aria-controls="mapping" role="tab" data-toggle="tab">N.T.O.S.
                            (TMU Notices)</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="facilities">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Facilities
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p>To create a sub-facility (ie, center area, terminal area) please email
                                    vatusa6@vatusa.net with the identifier desired
                                    (up to 4 letters) and English name. Identifiers to be related to the position, IE,
                                    TRACONs like M98, P31, etc. should use an identifier
                                    based on that. For center areas, use the IATA identifier with a letter or number
                                    area designator affixed. IE, area "A" of ZSE could be
                                    ZSEA.
                                </p>
                                <table class="table table-striped">
                                    @foreach($facilities as $facility)
                                        <tr>
                                            <td><b>{{$facility->id}}</b> - {{$facility->name}}</td>
                                            <td style="text-align: right">
                                                <button class="btn btn-success btnColors"
                                                        data-facility="{{$facility->id}}">Colors
                                                </button>
                                                <button class="btn btn-primary btnCoords"
                                                        data-facility="{{$facility->id}}">Boundary
                                                </button>
                                            </td>
                                        </tr>
                                        <tr style="display: none;" id="coords_{{$facility->id}}">
                                            <td colspan="2">
                                                <p class="alert alert-warning">Facility coordinates are entered in JSON
                                                    array format (<a href="https://www.javatpoint.com/json-array">example</a>)
                                                    of arrays containing floats representing latitude and longitude
                                                    in decimal degrees. The format is very specific and must be entered
                                                    correctly or it will not display correctly. When making changes, it
                                                    is recommended to check your JSON against
                                                    <a href="https://jsonlint.com/">JSONLint</a> to ensure it is valid
                                                    JSON. <b>The values must be float or decimal format, and cannot be
                                                        quoted.</b>
                                                    Formatting is optional, but proper care to ensure closures of
                                                    brackets [] and commas after all but the last array are required.
                                                    <b>The last point must be the same as the first point to close the
                                                        polygon.</b></p>
                                                <form id="boundaryForm" method="post"
                                                      action="/mgt/tmu/{{$facility->id}}/coords">
                                                    <textarea class="form-control" name="coords" rows="10"
                                                              id="coordbox_{{$facility->id}}">{{$facility->coords}}</textarea><br>
                                                </form>
                                                <button class="btn btn-primary btnSave"
                                                        data-facility="{{$facility->id}}">Save
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="notices">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    N.T.O.S.
                                </h3>
                                <p class="help-block">National Traffic Operations Status</p>
                            </div>
                            <div class="panel-body">
                                <h4><i class="fa fa-list"></i> Active and Future Notices</h4>
                                @php $allFacs = App\tmu_facilities::where('id', $fac)->orWhere('parent', $fac);
                                     $notices = App\TMUNotice::where('expire_date', '>=', \Illuminate\Support\Carbon::now('utc'))
                                                ->orWhereNull('expire_date')
                                                ->orderBy('priority', 'DESC')
                                                ->orderBy('start_date', 'DESC')
                                                ->orderBy('tmu_facility_id')
                                                ->where('tmu_facility_id', $allFacs->get()->pluck('id'))->get();
                                @endphp
                                <table class="table table-responsive table-striped" id="notices-table">
                                    <thead>
                                    <tr>
                                        <th style="width:10%;">Sector</th>
                                        <th style="width:15%;">Effective Date</th>
                                        <th style="width:45%;">Message</th>
                                        <th style="width:15%;">Expire Date</th>
                                        <th style="width:15%">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!$notices->count())
                                        <tr class="warning">
                                            <td colspan="5" style="text-align: center">
                                                <i class="fa fa-info-circle"></i> No Notices Active
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($notices as $notice)
                                            @php
                                                $priority = $notice->priority;
                                                switch($priority) {
                                                    case 1: $rcolor = 'warning'; break;
                                                    case 2: $rcolor =  'success'; break;
                                                    case 3: $rcolor = 'danger'; break;
                                                    default: $rcolor = ''; break;
                                                 }
                                            @endphp
                                            <tr class="{{ $rcolor }}" id="tmu-notice-{{ $notice->id }}">
                                                <td>{{ $notice->tmuFacility->name }}</td>
                                                <td>@if($notice->start_date > \Illuminate\Support\Carbon::now())
                                                        <i class="fa fa-calendar" rel="tooltip" title="Scheduled"></i>
                                                        <em> @endif {{ $notice->start_date->format('m/d/Y H:i') }} @if($notice->start_date > \Illuminate\Support\Carbon::now()) </em> @endif
                                                </td>
                                                <td>{!! $notice->message !!}</td>
                                                <td>{!! $notice->expire_date ? $notice->expire_date->format('m/d/Y H:i') : "<em>Indefinite</em>" !!}</td>
                                                <td>
                                                    <div>
                                                        <button class="btn btn-warning edit-notice"
                                                                data-id="{{$notice->id}}"><i class="fa fa-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-danger remove-notice"
                                                                data-id="{{ $notice->id }}"><i class="fa fa-remove"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                <hr>
                                <h4><i class="fa fa-plus"></i> Add New Notice</h4>
                                <p>Use this form to post public TMU Notices for your facilities.</p>
                                <form class="form-horizontal" id="new-notice">
                                    <div class="form-group">
                                        <label for="facility" class="col-sm-2 control-label">TMU Facility</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" name="facility" id="facility"
                                                    autocomplete="off">
                                                <option value="">-- Select One --</option>
                                                @foreach(\App\tmu_facilities::where('parent', $fac)->orWhere('id' , $fac)->orderBy('name')->get() as $tmufac)
                                                    <option value="{{ $tmufac->id }}">{{ $tmufac->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="priority1" class="col-sm-2 control-label">Priority</label>
                                        <div class="col-sm-10">
                                            <div class="has-warning">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="priority" id="priority1" value="1"
                                                               autocomplete="off">
                                                        1 - Low
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="has-success">
                                                <div class="radio success">
                                                    <label>
                                                        <input type="radio" name="priority" id="priority2" value="2"
                                                               checked autocomplete="off">
                                                        2 - Normal
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="has-error">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="priority" id="priority3" value="3"
                                                               autocomplete="off">
                                                        3 - Urgent
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="message" class="col-sm-2 control-label">Message</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="message" id="message"
                                                      autocomplete="off"></textarea>
                                            <p class="help-block">Use <code>&lt;b&gt;Bold&lt;/b&gt;</code>, <code>&lt;em&gt;Italics&lt;/em&gt;</code>,
                                                and <code>&lt;u&gt;Underline&lt;/u&gt;</code>
                                                for formatting.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="start-date" class="col-sm-2 control-label">Effective Date<br><em>Zulu
                                                Time</em></label>
                                        <div class="col-sm-10">
                                            <input type="text" id="start-date" name="start_date" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="hasExpires" class="col-sm-2 control-label">Expire Date<br><em>Zulu
                                                Time</em></label>
                                        <div class="col-sm-10">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="hasExpires" id="hasExpires"
                                                           autocomplete="off"> Has
                                                    Expiration Date
                                                </label>
                                            </div>
                                            <input type="text" id="expire-date" name="expire_date" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-success" id="submit-new-notice"><i
                                                    class="fa fa-check"></i>
                                                Post
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Edit Notice Modal -->
                                <div class="modal fade" id="edit-notice-modal" tabindex="-1" role="dialog"
                                     aria-labelledby="Edit Notice">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close"><span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title">Edit TMU Notice</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" id="edit-notice-form">
                                                    <input type="hidden" id="edit-notice-id" name="notice_id" value="0">
                                                    <div class="form-group">
                                                        <label for="facility-edit" class="col-sm-2 control-label">TMU
                                                            Facility</label>
                                                        <div class="col-sm-10">
                                                            <select class="form-control" name="facility"
                                                                    id="facility-edit"
                                                                    autocomplete="off">
                                                                <option value="">-- Select One --</option>
                                                                @foreach(\App\tmu_facilities::where('parent', $fac)->orWhere('id' , $fac)->orderBy('name')->get() as $tmufac)
                                                                    <option
                                                                        value="{{ $tmufac->id }}">{{ $tmufac->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="priority1-edit" class="col-sm-2 control-label">Priority</label>
                                                        <div class="col-sm-10">
                                                            <div class="has-warning">
                                                                <div class="radio">
                                                                    <label>
                                                                        <input type="radio" name="priority"
                                                                               id="priority1-edit" value="1"
                                                                               autocomplete="off">
                                                                        1 - Low
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="has-success">
                                                                <div class="radio success">
                                                                    <label>
                                                                        <input type="radio" name="priority"
                                                                               id="priority2-edit" value="2"
                                                                               checked autocomplete="off">
                                                                        2 - Normal
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="has-error">
                                                                <div class="radio">
                                                                    <label>
                                                                        <input type="radio" name="priority"
                                                                               id="priority3-edit" value="3"
                                                                               autocomplete="off">
                                                                        3 - Urgent
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="message-edit"
                                                               class="col-sm-2 control-label">Message</label>
                                                        <div class="col-sm-10">
                                            <textarea class="form-control" name="message" id="message-edit"
                                                      autocomplete="off"></textarea>
                                                            <p class="help-block">Use
                                                                <code>&lt;b&gt;Bold&lt;/b&gt;</code>, <code>&lt;em&gt;Italics&lt;/em&gt;</code>,
                                                                and <code>&lt;u&gt;Underline&lt;/u&gt;</code>
                                                                for formatting.</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="start-date-edit" class="col-sm-2 control-label">Effective
                                                            Date<br><em>Zulu
                                                                Time</em></label>
                                                        <div class="col-sm-10">
                                                            <input type="text" id="start-date-edit" name="start_date"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="hasExpires-edit" class="col-sm-2 control-label">Expire
                                                            Date<br><em>Zulu
                                                                Time</em></label>
                                                        <div class="col-sm-10">
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="hasExpires"
                                                                           id="hasExpires-edit"
                                                                           autocomplete="off"> Has
                                                                    Expiration Date
                                                                </label>
                                                            </div>
                                                            <input type="text" id="expire-date-edit" name="expire_date"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="button" class="btn btn-success" id="save-notice-edit"
                                                        data-loading-text="Saving..."><i
                                                        class="fa fa-check"></i> Save changes
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
      $(document).ready(function () {
        var hash = document.location.hash
        if (hash)
          $('.nav-tabs a[href=' + hash + ']').tab('show')

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
          window.location.hash = e.target.hash
        })

        $('#fac').change(function () {
          window.location = '/mgt/tmu/' + $('#fac').val()
        })

        $('.btnCoords').click(function () {
          $('#coords_' + $(this).data('facility')).toggle()
        })
        $('.btnColors').click(function () {
          window.location = '/mgt/tmu/' + $(this).data('facility') + '/colors'
        })
        $('.btnSave').click(function () {
          try {
            var fac = $(this).data('facility')
            var c = $.parseJSON($('#coordbox_' + fac).val())
          } catch (e) {
            bootbox.alert('The coordinates entered for ' + $(this).data('facility') + ' are not in the correct format.  Unable to continue.')
            return false
          }
          waitingDialog.show('Saving...')
          /*$.ajax({
              url: '/mgt/tmu/' + $(this).data("facility") + '/coords',
              method: "post",
              data: { coords: $('#coordbox_' + fac).val(), token: "" }
          }).always(function() {
              waitingDialog.hide();
          }).success(function() {
              bootbox.alert("Coordinates for " + fac + " saved.");
          }).fail(function() {
              bootbox.alert("There was an error saving coordinates.  Please try again later.");
          });*/
          $('#boundaryForm').submit()
        })
      })

      $(function () {
          let utcTime = moment().utc()
          $('#start-date').val(utcTime.format('Y-MM-DD HH:mm'))
          $('#start-date').datetimepicker({
            format          : 'Y-m-d H:i',
            formatDate      : 'Y-m-d',
            formatTime      : 'H:i',
            minDate         : utcTime.format('Y-MM-DD'),
            inline          : true,
            onChangeDateTime: function (ct) {
              $('#expire-date').datetimepicker('setOptions', {
                minDate: $('#start-date').val() ? moment($('#start-date').val()).format('Y-MM-DD') : false
              })
            }
          })

          $('#expire-date').datetimepicker({inline: true}).datetimepicker('destroy')
          $('#expire-date-edit').datetimepicker({inline: true}).datetimepicker('destroy')
          $('#hasExpires').change(function () {
            if ($(this).is(':checked')) {
              $('#expire-date').datetimepicker({
                format          : 'Y-m-d H:i',
                inline          : true,
                formatDate      : 'Y-m-d',
                formatTime      : 'H:i',
                minDate         : $('#start-date').val() ? moment($('#start-date').val()).format('Y-MM-DD') : utcTime.format('Y-MM-DD'),
                onChangeDateTime: function (ct) {
                  $('#start-date').datetimepicker('setOptions', {
                    maxDate: $('#expire-date').val() ? moment($('#expire-date').val()).format('Y-MM-DD') : false,
                  })
                }
              })
            } else {
              $('#expire-date').datetimepicker('destroy')
              $('#start-date').datetimepicker('setOptions', {
                maxDate: false
              })
            }
          })
          $('#hasExpires-edit').change(function () {
            if ($(this).is(':checked')) {
              $('#expire-date-edit').datetimepicker({
                format          : 'Y-m-d H:i',
                inline          : true,
                formatDate      : 'Y-m-d',
                formatTime      : 'H:i',
                minDate         : $('#start-date-edit').val() ? moment($('#start-date-edit').val()).format('Y-MM-DD') : utcTime.format('Y-MM-DD'),
                onChangeDateTime: function (ct) {
                  $('#start-date-edit').datetimepicker('setOptions', {
                    maxDate: $('#expire-date-edit').val() ? moment($('#expire-date-edit').val()).format('Y-MM-DD') : false,
                  })
                }
              })
            } else {
              $('#expire-date-edit').datetimepicker('destroy')
              $('#start-date-edit').datetimepicker('setOptions', {
                maxDate: false
              })
            }
          })

          $('#submit-new-notice').click(function (e) {
            e.preventDefault()
            let btn  = $(this),
                form = $('form#new-notice')
            btn.html('<i class=\'fa fa-spinner fa-spin\'></i> Posting...').attr('disabled', true)
            $.ajax({
              method: 'POST',
              url   : $.apiUrl() + '/v2/tmu/notices/',
              data  : $('#new-notice').serialize()
            })
              .done(function (result) {
                btn.html('<i class=\'fa fa-check\'></i> Post').attr('disabled', false)
                swal('Success!', 'The TMU Notice has been successfully posted.', 'success').then(() => { location.reload() })
              })
              .error(function (result) {
                btn.html('<i class=\'fa fa-check\'></i> Post').attr('disabled', false)
                swal('Error!', 'Could not post TMU notice. Error recieved: ' + result.responseJSON.msg, 'error')
              })
          })
          $('.remove-notice').click(function () {
            let id = $(this).data('id')
            swal({
              title     : 'Are you sure?',
              text      : 'This will delete the Notice. This action cannot be undone.',
              icon      : 'warning',
              buttons   : {
                cancel : 'No, cancel',
                confirm: {
                  text      : 'Yes, delete',
                  closeModal: false,
                  className : 'danger'
                }
              },
              dangerMode: true
            })
              .then(r => {
                if (r) {
                  $.ajax({
                    method: 'DELETE',
                    url   : $.apiUrl() + '/v2/tmu/notice/' + id,
                  })
                    .done(function (result) {
                      $('tr#tmu-notice-' + id).remove()
                      swal('Success!', 'The TMU Notice has been successfully deleted.', 'success')
                    })
                    .error(function (result) {
                      swal('Error!', 'Could not post TMU notice. Error recieved: ' + result.responseJSON.msg, 'error')
                    })
                }
              })
          })
          $('.edit-notice').click(function () {
            let btn = $(this),
                id  = btn.data('id')

            btn.prop('disabled', true)
            $.ajax({
              method: 'GET',
              url   : $.apiUrl() + '/v2/tmu/notice/' + id,
            })
              .done(function (result) {
                //Reset fields
                $('#start-date-edit').datetimepicker('destroy')
                $('#expire-date-edit').datetimepicker({inline: true}).datetimepicker('destroy')
                $('#hasExpires-edit').prop('checked', false)
                btn.prop('disabled', false)

                //Populate fields
                $('#edit-notice-id').val(id)
                $('#facility-edit').val(result.tmu_facility_id)
                for (let i = 1; i <= 3; i++)
                  $('#priority' + i + '-edit').prop('checked', false)
                $('#priority' + result.priority + '-edit').prop('checked', true)
                $('#message-edit').html(result.message)

                //Populate start date
                $('#start-date-edit').val(result.start_date.substr(0, result.start_date.length - 3))
                $('#start-date-edit').datetimepicker({
                  format          : 'Y-m-d H:i',
                  formatDate      : 'Y-m-d',
                  formatTime      : 'H:i',
                  minDate         : utcTime.format('Y-MM-DD'),
                  inline          : true,
                  onChangeDateTime: function (ct) {
                    $('#expire-date-edit').datetimepicker('setOptions', {
                      minDate: $('#start-date-edit').val() ? moment($('#start-date-edit').val()).format('Y-MM-DD') : false
                    })
                  }
                })

                if (result.expire_date !== null) {
                  //Populate expire date
                  $('#expire-date-edit').val(result.expire_date.substr(0, result.expire_date.length - 3))
                  $('#hasExpires-edit').prop('checked', true)
                  $('#expire-date-edit').datetimepicker({
                    format          : 'Y-m-d H:i',
                    inline          : true,
                    formatDate      : 'Y-m-d',
                    formatTime      : 'H:i',
                    minDate         : $('#start-date-edit').val() ? moment($('#start-date-edit').val()).format('Y-MM-DD') : 0,
                    onChangeDateTime: function (ct) {
                      $('#start-date-edit').datetimepicker('setOptions', {
                        maxDate: $('#expire-date-edit').val() ? moment($('#expire-date-edit').val()).format('Y-MM-DD') : false
                      })
                    }
                  })
                }
              })
              .error(function (result) {
                btn.prop('disabled', false)
                swal('Error!', 'Unable to retrieve TMU Notice data for editing. ' + result.responseJSON.msg, 'error')
              })

            $('#edit-notice-modal').modal('toggle')
          })
          $('#save-notice-edit').click(function () {
            let btn  = $(this),
                form = $('#edit-notice-form'),
                id   = $('#edit-notice-id').val()
            btn.button('loading')
            $.ajax({
              method: 'PUT',
              url   : $.apiUrl() + '/v2/tmu/notice/' + id,
              data  : form.serialize()
            })
              .done(function (result) {
                swal('Success!', 'The Notice has been edited.', 'success').then(() => { location.reload(true) })
              })
              .error(function (result) {
                btn.button('reset')
                swal('Error!', 'The Notice could not be edited. ' + result.responseJSON.msg, 'error')
              })
          })
        }
      )
    </script>
@endsection