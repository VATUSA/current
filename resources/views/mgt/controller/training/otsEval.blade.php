@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/otsEval.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush

@extends('layout')
@section('title', 'OTS Evaluation')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-pencil"></span> VATUSA Competency Review and Certification
                </h3>
            </div>
            <div class="panel-body">
                <article id="eval-container">
                    <!-- Collapsable Instructor Notes -->
                    <table class="table table-bordered" id="ots-eval-header">
                        <thead>
                        <tr>
                            <td colspan="5">
                                OTS Evaluation: {{ $form->name }}
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><p id="student-name" class="form-control-static">{{ $student->fullname() }}
                                    ({{ $student->cid }})</p><label class="table-cell-footer control-label"
                                                                    for="position">Student Name & CID</label></td>
                            <td>
                                <p id="student-position"><input type="text" maxlength="7" name="position"
                                                                id="position" class="form-control" autocomplete="off">
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="position">Operation Position</label>
                            </td>
                            <td>
                                <p>
                                    <input class="form-control" name="date" id="exam-date" value="{{ date('Y-m-d') }}"
                                           style="width:150px;" autocomplete="off">
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="exam-date">Date of Exam (UTC)</label>
                            </td>
                            <td>
                                <p id="student-facility"
                                   class="form-control-static">{{ $student->facility()->name }}</p>
                                <label class="table-cell-footer control-label"
                                       for="position">Facility</label></td>
                            <td style="width: 20%;">
                                <p>
                                <div id="form-actions" class="btn-group">
                                    <button class="btn btn-primary resetForm"><i
                                            class="fas fa-sync"></i> Reset Form
                                    </button>
                                    <button class="btn btn-danger"
                                            onclick="exitForm()">
                                        <i class="fas fa-times-circle"></i> Exit
                                    </button>
                                </div>
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="position">Form Actions</label></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-striped table-bordered" id="ots-eval-table">
                        <thead>
                        <tr>
                            <th>Performance Category</th>
                            <th>Performance Indicator</th>
                            <th class="indicator-res-header" data-value="0">
                                <div rel="tooltip"
                                     title="Not Observed">NA
                                </div>
                            </th>
                            <th class="indicator-res-header" data-value="1">
                                <div rel="tooltip"
                                     title="Commendable">C
                                </div>
                            </th>
                            <th class="indicator-res-header" data-value="2">
                                <div rel="tooltip"
                                     title="Satisfactory">S
                                </div>
                            </th>
                            <th class="indicator-res-header" data-value="3">
                                <div rel="tooltip"
                                     title="Unsatisfactory">U
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $catCount = 0; @endphp
                        @foreach($form->perfcats as $perfcat)
                            <tr>
                                <td rowspan="{{ $perfcat->indicators()->count() + 1 }}" class="perfcat-cell">
                                    <span class="perfcat-label-counter">{{ chr(65 + $catCount++) }}.</span>
                                    <span class="perfcat-label">{{ $perfcat->label }}</span>
                                </td>
                            </tr>
                            @php
                                $headerCount = 0;
                                $itemCount = 0;
                            @endphp
                            @foreach($perfcat->indicators as $indicator)
                                <tr>
                                    @if($indicator->header_type > 0)
                                        @php $itemCount = 0; @endphp
                                        <td class="indicator-header @if($indicator->header_type == 2) result-header @endif
                                        @if(in_array('bold',explode(',', $indicator->extra_options))) bold @endif">
                                            <span class="indicator-header-count">{{ ++$headerCount }}.</span>
                                            <span class="indicator-header-label">{{ $indicator->label }}</span>
                                        </td>
                                    @else
                                        <td class="indicator-item @if(in_array('bold',explode(',',$indicator->extra_options))) bold @endif">
                                            <div class="indicator-item-count">{{ chr(97 + $itemCount++) }}.</div>
                                            <div class="indicator-item-label"><span>{{ $indicator->label }}</div>
                                        </td>
                                    @endif
                                    @for($i = 0; $i < 4; $i++)
                                        @if($indicator->header_type == 1)
                                            <td class="result-cell result-na default-header">&nbsp;</td>
                                        @else
                                            @if(!$i && $indicator->is_required || $i == 1 && !$indicator->is_commendable
                                            || $i == 3 && !$indicator->can_unsat)
                                                <td class="result-cell result-na"><i class="fas fa-times"></i></td>
                                            @else
                                                <td class="result-cell"><input type="radio"
                                                                               name="result-{{ $indicator->id }}"
                                                                               data-id="{{ $indicator->id }}"
                                                                               class="form-control result-input"
                                                                               value="{{ $i }}" required
                                                                               autocomplete="off"></td>
                                            @endif
                                        @endif
                                    @endfor
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                    <table id="eval-submit-table" class="table table-striped table-bordered">
                        <tbody>
                        <tr>
                            <td>
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="result" class="col-sm-2 control-label">Exam Result</label>
                                        <div class="col-sm-10">
                                            <div class="btn-group" data-toggle="buttons" id="result">
                                                <label class="btn btn-success active ots-status-input-label">
                                                    <input type="radio" name="ots_result" id="ots-result-pass" value="1"
                                                           autocomplete="off" class="ots-status-input" checked>
                                                    <i class="fas fa-check"></i> Pass
                                                </label>
                                                <label class="btn btn-default ots-status-input-label">
                                                    <input type="radio" name="ots_status" id="ots-result-fail" value="2"
                                                           class="ots-status-input" autocomplete="off"><i
                                                        class="fas fa-times"></i> Fail
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <label for="notes" class="col-sm-2 control-label">Evaluation Notes</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="notes" name="notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label text-center">Examiner</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">{{ Auth::user()->fullname() }}
                                                ({{ Auth::user()->cid }})</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label">eSignature</label>
                                        <div class="col-sm-10">
                                            <div id="signature"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label">Current Date/Time</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static" id="currtime"></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-success" id="submit-eval"><i
                                                    class="fas fa-check-double"></i> eSign and Submit
                                            </button>
                                            <button class="btn btn-warning resetForm" type="button"><i
                                                    class="fas fa-sync"></i> Reset Form
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </article>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ secure_asset("datetimepicker/datetimepicker.js") }}"></script>
    <script type="text/javascript" src="{{ secure_asset("jSignature/jSignature.min.js") }}"></script>
    <script type="text/javascript" src="{{ secure_asset("jSignature/plugins/jSignature.UndoButton.js") }}"></script>
    <script type="text/javascript"
            src="{{ secure_asset("jSignature/plugins/signhere/jSignature.SignHere.js") }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{ secure_asset("datetimepicker/datetimepicker.js") }}"></script>
    <script type="text/javascript" src="{{ secure_asset("js/moment.js") }}"></script>
    <script src="https://unpkg.com/sticky-table-headers"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        window.onbeforeunload = function () {
          return true
        }
        $('#ots-eval-table').stickyTableHeaders()
        const position = Cookies.get('eval-pos') ?? "_{{ $form->position }}".toUpperCase(),
              jsDate   = new Date(),
              date     = Cookies.get('eval-date') ?? jsDate.getUTCFullYear() + '-' + pad(jsDate.getUTCMonth() + 1) + '-' + pad(jsDate.getUTCDate()),
              offset   = jsDate.getUTCFullYear() - jsDate.getFullYear() + 1
        Cookies.remove('eval-pos')
        Cookies.remove('eval-date')

        $('#signature').jSignature({UndoButton: true, height: '100px'})

        $('#exam-date').datetimepicker({
          timepicker: false,
          format    : 'Y-m-d',
          mask      : true,
          maxDate   : '+1970/01/0' + offset,
          step      : 15
        }).val(date)

        $('#position').val(position)

        $('input[name^="result"]').change(function () {
          $(this).parents('td').siblings().removeClass('danger success info warning')
          let color = ''
          switch (parseInt($(this).val())) {
            case 1:
              color = 'info'
              break
            case 2:
              color = 'success'
              break
            case 3:
              color = 'danger'
              break
          }
          $(this).parents('td').addClass(color)
        })

        $('td.result-cell:not(.result-na)').click(function () {
          $(this).find('input[type="radio"]').prop('checked', true).change()
        })
        $('.indicator-res-header').click(function () {
          let val = $(this).data('value')
          $('#ots-eval-table').find('input[type="radio"][value="' + val + '"]').prop('checked', true).change()
        })
          .mouseenter(function () {
            $(this).find('div').tooltip('show')
          }).mouseleave(function () {
          $(this).find('div').tooltip('hide')
        })

        $('.ots-status-input').change(function () {
          $('.ots-status-input').parent().attr('class', 'btn btn-default ots-status-input-label')
          let parent = $(this).parent()
          switch (parseInt($(this).val())) {
            case 1:
              parent.removeClass('btn-default').addClass('btn-success')
              break
            case 2:
              parent.removeClass('btn-default').addClass('btn-danger')
              break
            case 3:
              parent.removeClass('btn-default').addClass('btn-info')
              break
          }
        })

        $('.resetForm').click(function (e) {
          e.preventDefault()
          resetForm()
        })

        $('#submit-eval').click(function (e) {
          e.preventDefault()
          const btn       = $(this),
                position  = $('#position').val().toUpperCase(),
                date      = $('#exam-date').val(),
                result    = parseInt($('input[name="ots_result"]:checked').val()),
                notes     = $('#notes').val(),
                signature = $('#signature').jSignature('getData', 'svgbase64').join(',')

          if (!position || position.length !== 7 || !position.match(/^([A-Z0-9]{2,3})_(TWR|APP|CTR)$/))
            return swal('Invalid Position', 'The position field is invalid. Format: ABC_POS', 'error')
          if (!date || moment(moment().utc().format('YYYY-MM-DD')).diff(moment(date)) < 0)
            return swal('Invalid Date', 'The exam date is invalid. It must be in UTC and in the past.', 'error')
          if ($('#signature').jSignature('getData', 'base30')[1].length < 150)
            return swal('Invalid Signature', 'The signature has insufficient content.', 'error')

          let results = []
          $('input[name^="result"]:checked').each(function () {
            const input = $(this),
                  value = input.val(),
                  id    = input.data('id')
            results.push({id: id, value: value})
          })
          if (results.length !== {{ $form->indicators()->where('header_type', '!=', 1)->count() }})
            return swal('Missing Fields', 'Some indicators do not have a result selected. All fields are required.', 'error')

          btn.html('<i class="fas fa-spinner fa-spin"></i> Submitting...').prop('disabled', true)
          $.post($.apiUrl() + "/v2/user/{{ $student->cid }}/training/otsEval",
            {
              position: position,
              date    : date,
              result  : result,
              notes   : notes,
              signatur: signature,
              results : results
            },
            result => {
              btn.html('<i class="fas fa-check-double"></i> eSign and Submit').prop('disabled', false)
              if (result.status !== 'OK') {
                return swal('Error!', 'Unable to submit OTS evaluation. ' + result.msg + 'Please try again later.', 'error')
              }
              swal({
                title  : 'Success!',
                text   : 'The OTS evaluation has been successfully submitted.',
                icon   : 'success',
                buttons: {
                  join: {
                    text     : 'Return to Promotion Submission',
                    value    : 'return',
                    className: 'btn-success'
                  },
                  ok  : {
                    text: 'OK'
                  }
                }
              }).then(selection => {
                switch (selection) {
                  case 'return':
                    window.location = '{{ secure_url("mgt/controller/{$student->cid}/promote") }}'
                    break
                  default:
                    return
                }
              })
            }
          ).fail((xhr, status, error) => {
            btn.html('<i class="fas fa-check-double"></i> eSign and Submit').prop('disabled', false)
            return swal('Error!', 'Unable to submit OTS evaluation. Please try again later.', 'error')
          })
        })
      })

      const exitForm = () => {
        swal({
          title     : 'Are you sure?',
          text      : 'Once you leave, you must start the form over.',
          icon      : 'warning',
          buttons   : true,
          dangerMode: true,
        })
          .then((willDelete) => {
            if (willDelete) {
              window.onbeforeunload = null
              window.location = '{{ secure_url("mgt/controller/{$student->cid}/promote") }}'
            }
          })
      }
      const resetForm = () => {
        swal({
          title     : 'Are you sure?',
          text      : 'This will clear all selections in the evaluation.',
          icon      : 'warning',
          buttons   : true,
          dangerMode: true,
        })
          .then((willDelete) => {
            if (willDelete) {
              $('.result-input').prop('checked', false).parents('td').siblings().removeClass('danger success info warning').change()
              $('#notes').val('')
              $('#signature').jSignature('reset')
            }
          })
      }

      const pad = n => {
        if (n < 10) return '0' + n
      }
      const updateTime = () => {
        $('#currtime').html(moment().utc().format('dddd MMMM Do, YYYY h:mm:ss a'))
      }
      updateTime()
      setInterval(updateTime, 1000)
    </script>
@endsection