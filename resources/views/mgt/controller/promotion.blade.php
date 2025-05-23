@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush

@extends('layout')
@section('title', 'Controller Promotion')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-star"></span> Promotion Submission
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <form class="form-horizontal" id="promotion-form">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="student">Student</label>
                                <p class="col-sm-9 form-control-static" id="student">
                                    {{$user->fname}} {{$user->lname}} ({{$user->cid}})
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Rating</label>
                                <p class="col-sm-9 form-control-static">
                                    {{ \App\Classes\Helper::ratingShortFromInt($user->rating) }} <i
                                            class="fa fa-arrow-right text-success" style="padding:0 10px;"></i>
                                    <strong>{{ \App\Classes\Helper::ratingShortFromInt($user->rating + 1) }}</strong>
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Rating Grantor</label>
                                <p class="col-sm-9 form-control-static">
                                    {{\Auth::user()->fname}} {{\Auth::user()->lname}} ({{\Auth::user()->cid}})
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="exam-date">Date of Exam (UTC)</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="date" id="exam-date"
                                           value="{{ $dateOfExam ? explode(' ', $dateOfExam)[0] : date('Y-m-d') }}"
                                           style="width:150px;" autocomplete="off" @if($dateOfExam) disabled
                                           readonly @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="position">Exam Position</label>
                                <div class="col-sm-9">
                                    <input type="text" name="position" id="position" placeholder="ABC_APP"
                                           class="form-control" style="width:150px;"
                                           value="{{ $examPosition ?? '' }}"
                                           maxlength="11"
                                           autocomplete="off" @if($examPosition) readonly disabled @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Training Record</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    @switch($trainingRecordStatus)
                                        @case(1)
                                            <span class="label label-success" style="font-size:90%;"><span
                                                        class="glyphicon glyphicon-ok"></span> Exists</span>
                                            @break
                                        @case(0)
                                            <span class="label label-danger" style="font-size:90%;" rel="tooltip"
                                                  title="There must be a training record present in the CTRS that is marked Rating Exam Pass under the specified position."
                                                  onclick="window.open('/mgt/controller/{{ $user->cid }}#training', 'blank')"><span
                                                        class="glyphicon glyphicon-remove"></span> Does Not Exist</span>
                                            @break
                                        @default
                                            <span class="label label-default" style="font-size:90%;"><i
                                                        class="fas fa-times-circle"></i> Not Applicable</span>
                                            <br>
                                            @break
                                    @endswitch
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Rating Exam Evaluation</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    @switch($otsEvalStatus)
                                        @case(-1)
                                            <span class="label label-default" style="font-size:90%;"><i
                                                        class="fas fa-times-circle"></i> Not Applicable</span>
                                            <br>
                                            @break
                                        @case(0)
                                            <span class="label label-danger" style="font-size:90%;" rel="tooltip"
                                                  title="The Rating Exam evalulation form must be completely and correctly filled out on the right."><span
                                                        class="glyphicon glyphicon-remove"></span> Not Complete</span>
                                            @break
                                        @case(1)
                                            <span class="label label-success" style="font-size:90%;"><span
                                                        class="glyphicon glyphicon-ok"></span> Complete</span>
                                            @break
                                        @case(2)
                                            <span class="label label-warning" style="font-size:90%;"><span
                                                        class="glyphicon glyphicon-remove"></span> Not Passed</span>
                                            @break
                                    @endswitch
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-3">
                                    <button type="button" id="submit-promotion" class="btn btn-success btn-block"
                                            style="width:150px;"
                                            @if(!($otsEvalStatus == 1 && $trainingRecordStatus == 1)) disabled @endif>
                                        <i class="fas fa-check"></i> Promote
                                    </button>
                                    <button type="button" id="cancel-promotion" class="btn btn-danger btn-block"
                                            style="width:150px;">
                                        <i class="fas fa-times"></i> Don't Promote
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-warning"><span class="glyphicon glyphicon-info-sign"></span>
                                If you press the Promote button, the controller will
                                be promoted even if they have failed the Rating Exam!
                                Please only hit Promote if you mean it!
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6" style="position: absolute;left: 40%;width: 40%; max-width: 800px">
                        <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Enter the
                            position on the left to view the applicable Rating Exam Evaluation Form.
                        </div>
                        <div class="list-group">
                            @foreach($forms as $form)
                                @php
                                    $disableClass = "";
                                     if(!$dateOfExam)
                                         $disableClass = $form->rating_id == $user->rating + 1 ? 'temp-disabled disabled' : 'disabled';
                                     else
                                           $disableClass = $form->rating_id == $user->rating + 1 ? '' : 'disabled';
                                @endphp
                                <a class="list-group-item list-group-item list-group-item-{{ $dateOfExam ? 'success': 'info' }} eval-link {{ $disableClass }}"
                                   id="{{ strtolower($form->position) }}" data-id="{{ $form->id }}"
                                   data-statement="{{ $form->is_statement }}" href="#">
                                    <h4 class="list-group-item-heading">{{ $form->name }}
                                        @if($dateOfExam && $form->rating_id == $user->rating + 1)
                                            <span
                                                    class="glyphicon glyphicon-ok pull-right"></span>
                                        @elseif($dateOfExam)
                                            <span
                                                    class="glyphicon glyphicon-remove pull-right"></span>
                                        @else
                                            <span
                                                    class="glyphicon glyphicon-{{$form->is_statement ? 'share' : 'arrow-right'}} pull-right"></span>
                                        @endif
                                    </h4>
                                    <p class="list-group-item-text">{!! $form->description !!}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{ secure_asset("datetimepicker/datetimepicker.js") }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            /*$('[name=examiner]').autocomplete({
              source   : '/ajax/cid',
              minLength: 2,
              select   : function (event, ui) {
                $('[name=examiner]').val(ui.item.value)

                return false
              }
            })*/

            const jsDate = new Date(),
                offset = jsDate.getUTCFullYear() - jsDate.getFullYear() + 2

            $('#exam-date').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                mask: true,
                maxDate: '+1970/01/0' + offset,
                step: 15
            })

            $('#position').keyup(function () {
                $(this).val($(this).val().toUpperCase())
                $('.list-group-item:not(.disabled)').addClass('disabled')
                let pos = $(this).val().split('_')
                if ((pos.length === 2 || pos.length === 3) && (pos.length >= 2 && pos[pos.length - 1].length === 3)) {
                    pos = pos.pop()
                    if (pos !== '') {
                        $('#' + pos.toLowerCase() + '.temp-disabled').removeClass('disabled')
                    } else $('#submit-promotion').attr('disabled', true)
                } else
                    $('#submit-promotion').attr('disabled', true)
            })

            $('.eval-link').click(function (e) {
                e.preventDefault()
                if ($(this).hasClass('disabled')) return
                const link = $(this),
                    id = link.data('id'),
                    statement = link.data('statement'),
                    description = link.find('p').html()
                if (statement) {
                    return swal({
                        title: 'Delivery and Ground Certification Statement',
                        text: description,
                        icon: 'info',
                        buttons: {
                            confirm: {
                                text: 'eSign and Certify',
                                className: 'btn-success'
                            }
                        }
                    }).then(r => {
                        if (r) {
                            swal('DEL/GND Certification Complete', 'You may now proceed with the promotion.', 'success')
                            $('#submit-promotion').attr('disabled', false)
                            $('a.list-group-item[data-id="' + id + '"]').removeClass('list-group-item-info disabled').addClass('list-group-item-success')
                                .find('span.glyphicon').removeClass('glyphicon-share').addClass('glyphicon-ok')
                        }
                    })
                }
                Cookies.set('eval-pos', $('#position').val())
                Cookies.set('eval-date', $('#exam-date').val())
                window.location = '{{ $dateOfExam ? "/mgt/facility/training/eval/$evalId/view" : 'eval'}}'
            })

            $('#submit-promotion').click(function (e) {
                let btn = $(this)
                btn.attr('disabled', true).html('<i class="fas fa-spin fa-spinner"></i> Promoting...')
                e.preventDefault()
                $.post($.apiUrl() + "/v2/user/{{ $user->cid }}/rating", {
                    cid: {{ $user->cid }},
                    rating: {{ $user->rating + 1 }},
                    examDate: $('#exam-date').val(),
                    position: $('#position').val()
                }, result => {
                    btn.attr('disabled', false).html('<i class="fas fa-check"></i> Promote')
                    if (result.data.hasOwnProperty('status') && result.data.status === 'OK') {
                        return swal('Success!', 'The controller has been promoted.', 'success').then(() => {
                            return window.location = '{{ url('/mgt/facility') }}'
                        })
                    } else
                        return swal('Error!', 'The controller was not promoted. ' + result.data.msg, 'error')
                }).fail(_ => {
                    btn.attr('disabled', false).html('<i class="fas fa-check"></i> Promote')
                    return swal('Error!', 'The controller was not promoted. Please ensure all fields and requirements are completed or try again later.', 'error')
                })
            });
            $('#cancel-promotion').click(function (e) {
                window.location = '{{ url('/mgt/facility') }}'
            });
        })
    </script>
@endsection
