@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/otsEval-v2.3.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset("datetimepicker/datetimepicker.css") }}">
@endpush

@extends('layout')
@section('title', 'Rating Exam Evaluation Statistics')
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
                            <td colspan="3">
                                {{ $form->name }}
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <canvas id="line-1" height="220px"></canvas>
                            </td>
                            <td @if(!$facility) colspan="2" @endif>
                                <canvas id="stacked-1" height="220px"></canvas>
                            </td>
                            @if($facility)
                                <td>
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Instructor</th>
                                            <th>Num Passes/Fails</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tableData as $data)
                                            <tr>
                                                <td>{{ $data['name'] }} <span class="sparkline-tri"
                                                                              values="{{ $data['sparkline'] }}"></span>
                                                </td>
                                                <td><span class="text-success" rel="tooltip"
                                                          title="Past 30 days">{{ $data['numPasses'][30] }}</span>/
                                                    <span class="text-success" rel="tooltip"
                                                          title="Past 60 days">{{ $data['numPasses'][60] }}</span>/
                                                    <span class="text-success" rel="tooltip"
                                                          title="Past 90 days">{{ $data['numPasses'][90] }}</span>
                                                    <br>
                                                    <span class="text-danger" rel="tooltip"
                                                          title="Past 30 days">{{ $data['numFails'][30] }}</span>/
                                                    <span class="text-danger" rel="tooltip"
                                                          title="Past 60 days">{{ $data['numFails'][60] }}</span>/
                                                    <span class="text-danger" rel="tooltip"
                                                          title="Past 90 days">{{ $data['numFails'][90] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>
                                <p id="student-facility"
                                   @if(!$hasGlobalAccess) class="form-control-static" @endif>
                                @if(!$hasGlobalAccess) {{ $facility->name }} @else
                                    <form class="form-inline" action="{{ url("mgt/facility/training/eval/{$form->id}/stats") }}" method="POST"
                                          id="training-artcc-select-form">
                                        <div class="form-group">
                                            <select class="form-control" id="tng-artcc-select" autocomplete="off" name="facility">
                                                <option value="" @if(!$facility) selected @endif>All Facilities</option>
                                                    @foreach($facilities as $fac)
                                                        <option value="{{ $fac->id }}"
                                                                @if($facility && $facility->id == $fac->id) selected @endif>{{ $fac->name }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </form>
                                    @endif
                                    </p>
                                    <label class="table-cell-footer control-label"
                                           for="tng-artcc-select">Facility</label></td>
                            <td>
                                <p id="student-position">
                                <form class="form-inline" action="{{ url("mgt/facility/training/eval/{$form->id}/stats") }}" method="POST"
                                      id="training-instructor-select-form">
                                    <input type="hidden" name="facility" value="{{ $facility->id ?? ""}}">
                                    <select class="form-control" id="instructor-select" name="instructor"
                                            @if(!$facility) disabled @endif>
                                        @if($facility)
                                            <option value="">All Instructors</option>
                                            @foreach($allIns as $ins)
                                                <option value="{{ $ins['cid'] }}"
                                                        @if($instructor == $ins['cid']) selected @endif>
                                                    {{ $ins['name'] }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" selected>-- Select Facility --</option>
                                        @endif
                                    </select>
                                </form>
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="instructor-select">Instructor</label>
                            </td>
                            <td>
                                <p>
                                <form class="form-inline" action="{{ url("mgt/facility/training/eval/{$form->id}/stats") }}" method="POST"
                                      id="exam-interval-select-form">
                                    <input type="hidden" name="facility" value="{{ $facility->id ?? "" }}">
                                    <input type="hidden" name="instructor" value="{{ $instructor }}">
                                    <input class="form-control text-center" name="interval" id="exam-interval"
                                           value="{{ $interval }}"
                                           style="width:100px;" autocomplete="off" rel="tooltip"
                                           title="This number dictates the amount of exams that are used in calculating itemized statistics.">
                                </form>
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
                                            <span class="indicator-header-label">{!! $indicator->label !!}
                                                @if($indicator->help_text)
                                                    <span class="indicator-help-text" data-toggle="popover"
                                                          title="Instructions"
                                                          data-content="{{ $indicator->help_text }}"><i
                                                            class="fas fa-question-circle"></i></span>
                                                @endif</span>
                                        @if($indicator->header_type == 2)
                                            <td class="indicator-comment-cell">&nbsp;</td>
                                        @endif
                                    @else
                                        <td class="indicator-item @if(in_array('bold',explode(',',$indicator->extra_options))) bold @endif">
                                            <div class="indicator-item-count">{{ chr(97 + $itemCount++) }}.</div>
                                            <div class="indicator-item-label">
                                                <span>{!! $indicator->label !!}</span>
                                                @if($indicator->help_text)
                                                    <span class="indicator-help-text" data-toggle="popover"
                                                          title="Instructions"
                                                          data-content="{{ $indicator->help_text }}"><i
                                                            class="fas fa-question-circle"></i>
                                                        </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="indicator-comment-cell">&nbsp;</td>
                                    @endif
                                    @php $colors = ['', 'info', 'success', 'danger']; @endphp
                                    @if($indicator->header_type != 1) @php $percents = \App\Models\OTSEvalIndResult::getPercentages($indicator->id, $facility->id ?? null, $instructor, $interval); @endphp @endif
                                    @for($i = 0; $i < 4; $i++)
                                        @if($indicator->header_type == 1)
                                            <td class="result-cell result-na default-header">&nbsp;</td>
                                        @else
                                            @if(!$i && $indicator->is_required || $i == 1 && !$indicator->is_commendable
                                            || $i == 3 && !$indicator->can_unsat)
                                                <td class="result-cell result-na"><i class="fas fa-times"></i></td>
                                            @else
                                                <td class="result-cell text-{{ $colors[$i] }}">{{ $percents[$i] }}</td>
                                            @endif
                                        @endif
                                    @endfor
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </article>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset("datetimepicker/datetimepicker.js") }}"></script>
    <script type="text/javascript" src="{{ asset("jSignature/jSignature.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("jSignature/plugins/jSignature.UndoButton.js") }}"></script>
    <script type="text/javascript"
            src="{{ asset("jSignature/plugins/signhere/jSignature.SignHere.js") }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{ asset("datetimepicker/datetimepicker.js") }}"></script>
    <script type="text/javascript" src="{{ asset("js/moment.js") }}"></script>
    <script src="https://unpkg.com/sticky-table-headers"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="{{ asset("js/jquery.sparkline.js") }}"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('[data-toggle="popover"]').popover({trigger: 'hover'})

        $('#ots-eval-table').stickyTableHeaders()

        $('.indicator-res-header').mouseenter(function () {
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

        const stacked1 = new Chart($('#stacked-1'), {
          type   : '{{ !$facility || $instructor ? 'bar' : 'line' }}',
          data   : {!! json_encode($evalsPerMonthDataIns) !!},
          options: {
            scales                  : {
              xAxes: [{
                stacked: true
              }],
              yAxes: [{
                stacked: true,
                ticks  : {
                  min: 0
                }
              }]
            },
              @if($instructor) title: {
                display: true,
                text   : 'Filter: {{ \App\Classes\Helper::nameFromCID($instructor) }}'
              } @endif
          }
        })
        const line1 = new Chart($('#line-1'), {
          type   : 'line',
          data   : {!! json_encode($numPassFailsData) !!},
          options: {
            scales                  : {
              yAxes: [{
                ticks: {
                  stacked: false,
                  min    : 0
                }
              }]
            },
              @if($instructor) title: {
                display: true,
                text   : 'Filter: {{ \App\Classes\Helper::nameFromCID($instructor) }}'
              } @endif
          }
        })
      })
      $('#tng-artcc-select').change(function () {
        $('#training-artcc-select-form').submit()
      })
      $('#instructor-select').change(function () {
        $('#training-instructor-select-form').submit()
      })
      $('#exam-interval').blur(function () {
        if ($(this).val() && $(this).val() != {{ $interval }} && parseInt($(this).val())) $('#exam-interval-select-form').submit()
      })
    </script>
@endsection
