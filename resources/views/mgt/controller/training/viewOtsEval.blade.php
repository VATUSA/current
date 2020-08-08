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
                                OTS Evaluation: {{ $eval->form->name }}
                            </td>
                        </tr>
                        </thead>
                        <tbody id="view-eval-header-body">
                        <tr>
                            <td><p id="student-name" class="form-control-static">{{ $student->fullname() }}
                                    ({{ $student->cid }})</p><label class="table-cell-footer control-label"
                                                                    for="position">Student Name & CID</label></td>
                            <td>
                                <p id="student-position" class="form-control-static"> {{ $eval->exam_position }}</p>
                                <label class="table-cell-footer control-label"
                                       for="position">Exam Position</label>
                            </td>
                            <td>
                                <p class="form-control-static">
                                    {{ $eval->exam_date }}
                                </p>
                                <label class="table-cell-footer control-label"
                                       for="exam-date">Date of Exam (UTC)</label>
                            </td>
                            <td>
                                <p id="student-facility"
                                   class="form-control-static">{{ $eval->facility->name }}</p>
                                <label class="table-cell-footer control-label"
                                       for="position">Facility</label></td>
                            <td>
                                <p id="ots-attempt"
                                   class="form-control-static">{{ $attempt }}</p>
                                <label class="table-cell-footer control-label"
                                       for="position">Attempt</label></td>
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
                        @foreach($eval->form->perfcats as $perfcat)
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
                                                @if($indicator->result($eval->id) && $indicator->result($eval->id)->comment)
                                                    <br> <span
                                                        class="indicator-comment-display"
                                                        id="indicator-comment-display-{{ $indicator->id }}">{{ $indicator->result($eval->id)->comment }}</span>@endif</span>
                                            @if($indicator->help_text)
                                                <span class="indicator-help-text" data-toggle="popover"
                                                      title="Instructions" data-content="{{ $indicator->help_text }}"><i
                                                        class="fas fa-question-circle"></i></span>
                                    @endif
                                    @if($indicator->header_type == 2)
                                        <td class="indicator-comment-cell">&nbsp;</td>
                                    @endif
                                    @else
                                        <td class="indicator-item @if(in_array('bold',explode(',',$indicator->extra_options))) bold @endif">
                                            <div class="indicator-item-count">{{ chr(97 + $itemCount++) }}.</div>
                                            <div class="indicator-item-label">
                                                <span>{!! $indicator->label !!}</span>
                                                @if($indicator->result($eval->id)->comment)<br><span
                                                    class="indicator-comment-display"
                                                    id="indicator-comment-display-{{ $indicator->id }}">{{ $indicator->result($eval->id)->comment }}</span>@endif
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
                                    @php $colors = ['', 'info', 'success', 'danger'] @endphp
                                    @for($i = 0; $i < 4; $i++)
                                        @if($indicator->header_type == 1)
                                            <td class="result-cell result-na default-header">&nbsp</td>
                                        @else
                                            @if(!$i && $indicator->is_required || $i == 1 && !$indicator->is_commendable
                                            || $i == 3 && !$indicator->can_unsat)
                                                <td class="result-cell result-na"><i class="fas fa-times"></i></td>
                                            @else
                                                @if($indicator->result($eval->id)->result == $i)
                                                    <td class="result-cell {{ $colors[$i] }}">
                                                        <i class="fas fa-check"></i>
                                                    </td>
                                                @else
                                                    <td class="result-cell">&nbsp;</td>
                                                @endif
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
                                            <p class="form-control-static">
                                                @if($eval->result)
                                                    <span class="label label-success" id="training-ots-exam-pass">
                                                    <span class="glyphicon glyphicon-ok"></span> Pass</span>
                                                @else
                                                    <span class="label label-danger" id="training-ots-exam-fail"><span
                                                            class="glyphicon glyphicon-remove"></span> Fail</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label text-center">Evaluation
                                            Notes</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-static">
                                                @if($eval->notes){!! $eval->notes !!}
                                                @else
                                                    <em>None</em>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label text-center">Recommending
                                            Parties</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">
                                                @if($recs->count())
                                                    {!!  implode('<br> ', $recs->map(function($v, $k) {
                                                            return \App\Classes\Helper::nameFromCID($v) . " ($v) on " .
                                                                \Carbon\Carbon::createFromTimeString($k)->format('Y-m-d');
                                                            })->all()) !!}
                                                @else
                                                    <em>None</em>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label text-center">Examiner</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">{{ $eval->instructor->fullname() }}
                                                ({{ $eval->instructor->cid }})</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label">eSignature</label>
                                        <div class="col-sm-10">
                                            {!! base64_decode(explode(',', $eval->signature)[1]) !!}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes" class="col-sm-2 control-label">Submitted Date</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">{{ $eval->created_at }}</p>
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

        $('[data-toggle="popover"]').popover({trigger: 'hover'})

        $('#ots-eval-table').stickyTableHeaders()

        $('#signature').jSignature({UndoButton: true, height: '100px'})

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
      })
    </script>
@endsection