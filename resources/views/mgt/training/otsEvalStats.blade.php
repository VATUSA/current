@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/otsEval.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush

@extends('layout')
@section('title', 'OTS Evaluation')
@section('content')
    <div id="scroll-control" class="btn-group btn-group-lg">
        <button class="btn btn-default" id="scroll-top"><i class="fas fa-angle-double-up"></i></button>
        <button class="btn btn-default" id="scroll-bottom"><i class="fas fa-angle-double-down"></i></button>
    </div>
    <div class="container" id="eval-outer-container">
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
                            <td>
                                <canvas id="line-1" height="220px"></canvas>
                            </td>
                            <td>
                                <canvas id="bar-1" height="220px"></canvas>
                            </td>
                            <td>
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Num Passes (30/60/90 days)</th>
                                        <th>Num Fails (30/60/90 days)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($tableData as $data)
                                        <tr>
                                            <td>{{ $data['name'] }} <span class="sparline-tri"
                                                                          values="{{ $data['sparkline'] }}"></span></td>
                                            <td><span class="text-success">{{ $data['numPasses'][30] }}</span>/
                                                <span class="text-success">{{ $data['numPasses'][60] }}</span>/
                                                <span class="text-success">{{ $data['numPasses'][90] }}</span>
                                            </td>
                                            <td><span class="text-danger">{{ $data['numFails'][30] }}</span>/
                                                <span class="text-danger">{{ $data['numFails'][60] }}</span>/
                                                <span class="text-danger">{{ $data['numFails'][90] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p id="student-facility"
                                   @if(!$hasGlobalAccess) class="form-control-static" @endif>
                                @if(!$hasGlobalAccess) {{ $facility->name }} @else
                                    <form class="form-inline" action="{{ Request::url() }}" method="POST"
                                          id="training-artcc-select-form">
                                        <div class="form-group">
                                            <select class="form-control" id="tng-artcc-select" autocomplete="off"
                                                    name="facility">
                                                <option value="" @if(!$trainingfac) selected @endif>-- Select One --
                                                </option>
                                                <optgroup label="Western Region">
                                                    @foreach($facilities->filter(function($fac) { return $fac->region == 7; }) as $fac)
                                                        <option value="{{ $fac->id }}"
                                                                @if($facility->id == $fac->id) selected @endif>{{ $fac->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Southern Region">
                                                    @foreach($facilities->filter(function($fac) { return $fac->region == 8; }) as $fac)
                                                        <option value="{{ $fac->id }}"
                                                                @if($fac->id == $fac->id) selected @endif>{{ $fac->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Northeastern Region">
                                                    @foreach($facilities->filter(function($fac) { return $fac->region == 8; }) as $fac)
                                                        <option value="{{ $fac->id }}"
                                                                @if($fac->id == $fac->id) selected @endif>{{ $fac->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </form>
                                    @endif
                                    </p>
                                    <label class="table-cell-footer control-label"
                                           for="tng-artcc-select">Facility</label></td>
                            <td>
                                <p id="student-position">
                                <form class="form-inline" action="{{ Request::url() }}" method="POST"
                                      id="training-instructor-select-form">
                                    <input type="hidden" name="facility" value="{{ $facility }}">
                                    <select class="form-control" id="instructor-select" name="instructor">
                                        @if($facility)
                                            <option value="">All Instructors</option>
                                            @foreach($allIns as $ins)
                                                <option value="{{ $ins }}" @if($instructor == $ins) selected @endif>
                                                    {{ \App\Classes\Helper::nameFromCID($ins) }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" selected disabled>-- Select Facility --</option>
                                        @endif
                                    </select>
                                </form>
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="instructor-select">Instructor</label>
                            </td>
                            <td>
                                <p>
                                    <input class="form-control" name="interval" id="exam-interval"
                                           value="{{ $interval }}"
                                           style="width:100px;" autocomplete="off" rel="tooltip"
                                           title="This number dictates the amount of exams that are used in calculating itemized statistics.">
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="exam-interval">Itemized Exam Interval</label>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-striped table-bordered" id="ots-eval-table">
                        <thead>
                        <tr>
                            <th>Performance Category</th>
                            <th colspan="2">Performance Indicator</th>
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
                                        @if(in_array('bold',explode(',', $indicator->extra_options))) bold @endif"
                                            @if($indicator->header_type != 2) colspan="2" @endif>
                                            <span class="indicator-header-count">{{ ++$headerCount }}.</span>
                                            <span class="indicator-header-label">{!! $indicator->label !!}<span
                                                    class="indicator-comment-display"
                                                    id="indicator-comment-display-{{ $indicator->id }}"></span></span>
                                            @if($indicator->help_text)
                                                <span class="indicator-help-text" data-toggle="popover"
                                                      title="Instructions" data-content="{{ $indicator->help_text }}"><i
                                                        class="fas fa-question-circle"></i></span>
                                    @endif
                                    @if($indicator->header_type == 2)
                                        <td class="indicator-comment-cell"><span class="indicator-comment" rel="tooltip"
                                                                                 title="Add Comment"
                                                                                 data-id="{{ $indicator->id }}"><i
                                                    class="fas fa-plus-circle"></i></span>
                                        </td>
                                    @endif
                                    @else
                                        <td class="indicator-item @if(in_array('bold',explode(',',$indicator->extra_options))) bold @endif">
                                            <div class="indicator-item-count">{{ chr(97 + $itemCount++) }}.</div>
                                            <div class="indicator-item-label">
                                                <span>{!! $indicator->label !!}</span>
                                                <span class="indicator-comment-display"
                                                      id="indicator-comment-display-{{ $indicator->id }}"></span>
                                                @if($indicator->help_text)
                                                    <span class="indicator-help-text" data-toggle="popover"
                                                          title="Instructions"
                                                          data-content="{{ $indicator->help_text }}"><i
                                                            class="fas fa-question-circle"></i>
                                                        </span>
                                                @endif</div>
                                        </td>
                                        <td class="indicator-comment-cell"><span class="indicator-comment" rel="tooltip"
                                                                                 id="indicator-comment-btn-{{ $indicator->id }}"
                                                                                 title="Add Comment"
                                                                                 data-id="{{ $indicator->id }}"><i
                                                    class="fas fa-plus-circle"></i></span>
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
                                                    <input type="radio" name="ots_status" id="ots-result-fail" value="0"
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
                                            <input type="hidden" name="form" id="form-id" value="{{ $form->id }}">
                                            <button type="submit" class="btn btn-success" id="submit-eval"><i
                                                    class="fas fa-check-double"></i> eSign and Submit
                                            </button>
                                            <button class="btn btn-warning resetForm" type="button"><i
                                                    class="fas fa-sync"></i> Reset Form
                                            </button>
                                            <div class="alert alert-info" style="margin-top: 5px;">
                                                <i class="fas fa-info-circle" style="display: table-cell"></i>
                                                <p style="display: table-cell; padding-left: 5px;"> By submitting this
                                                    form, you agree
                                                    that you are the examining instructor and have conducted the OTS
                                                    to the standards set forth by the VATUSA training staff and by
                                                    your own ARTCC. You also agree that all data and selections are
                                                    accurate to the best of your ability. <br><strong>Ensure that the
                                                        exam date is accurate, in UTC time, and that it matches the
                                                        related training record.</strong><br><strong
                                                        class="text-danger">Once submitted, it
                                                        cannot be modified or deleted.</strong></p></div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="{{ secure_asset("js/jquery.sparkline.js") }}"></script>
    <script type="text/javascript">
      $(document).ready(function () {

        $('[data-toggle="popover"]').popover({trigger: 'hover'})

        $('#ots-eval-table').stickyTableHeaders()

        $('#position').val(position)

        $('.indicator-res-header').click(function () {
          let val = $(this).data('value')
          $('#ots-eval-table').find('input[type="radio"][value="' + val + '"]').prop('checked', true).change()
        })
          .mouseenter(function () {
            $(this).find('div').tooltip('show')
          }).mouseleave(function () {
          $(this).find('div').tooltip('hide')
        })

        $('#scroll-top').click(function () {
          $('html, body').animate({scrollTop: 0}, 700)
        })
        $('#scroll-bottom').click(function () {
          $('html, body').animate({scrollTop: $(document).height()}, 700)
        })
        $('.sparkline-tri').sparkline('html', {
          type               : 'tristate',
          tooltipFormat      : ' <span style="color: @{{color}}">&#9679;</span> @{{value:result}}</span>',
          tooltipValueLookups: {result: {'-1': 'Fail', '1': 'Pass'}}
        })
      })
    </script>
@endsection