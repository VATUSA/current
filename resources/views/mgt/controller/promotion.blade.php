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
                                    {{$u->fname}} {{$u->lname}} ({{$u->cid}})
                                </p>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Rating</label>
                                <p class="col-sm-9 form-control-static">
                                    {{ \App\Classes\Helper::ratingShortFromInt($u->rating) }} <i
                                        class="fa fa-arrow-right text-success" style="padding:0 10px;"></i>
                                    <strong>{{ \App\Classes\Helper::ratingShortFromInt($u->rating + 1) }}</strong>
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
                                    <input class="form-control" name="date" id="exam-date" value="{{ date('Y-m-d') }}"
                                           style="width:150px;" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="position">Exam Position</label>
                                <div class="col-sm-9">
                                    <input type="text" name="position" id="position" placeholder="Position (ABC_CTR)"
                                           class="form-control" style="width:150px;" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Training Record</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    <span class="label label-danger" style="font-size:90%;" rel="tooltip"
                                          title="There must be a training record present in the CTRS that is marked OTS Pass under the specified position."><span
                                            class="glyphicon glyphicon-remove"></span> Does Not Exist</span>
                                    <br>
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>OTS Evaluation</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    <span class="label label-danger" style="font-size:90%;" rel="tooltip"
                                          title="The OTS evalulation form must be completely and correctly filled out on the right."><span
                                            class="glyphicon glyphicon-remove"></span> Not Complete</span>
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-offset-3">
                                    <button type="submit" class="btn btn-success btn-block" style="width:150px;"
                                            disabled><span
                                            class="glyphicon glyphicon-ok"></span> Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6" style="position: absolute;left: 40%;width: 40%;">
                        <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Enter the
                            position on the left to view the applicable OTS Evaluation Form.
                        </div>
                        <div class="list-group">
                            <a class="list-group-item list-group-item list-group-item-success disabled" id="gnd">
                                <h4 class="list-group-item-heading">Delivery and Ground Certification Statement<span
                                        class="glyphicon glyphicon-share pull-right"></span></h4>
                                <p class="list-group-item-text">I certify that via observations of the
                                    student/developmental controller’s training that s/he meets the required standards
                                    for Delivery/Ground certification as outlined in VATUSA Job Order 3120.4 (and
                                    alphabetical revisions thereof).</p>
                            </a>
                            <a class="list-group-item list-group-item list-group-item-success disabled" id="twr">
                                <h4 class="list-group-item-heading">S2 (Tower) Rating Review Form<span
                                        class="glyphicon glyphicon-arrow-right pull-right"></span></h4>
                                <p class="list-group-item-text">VATUSA Competency Review and Certification for Tower</p>
                            </a>
                            <a class="list-group-item list-group-item-success disabled" id="app">
                                <h4 class="list-group-item-heading">S3 (Approach) Rating Review Form<span
                                        class="glyphicon glyphicon-arrow-right pull-right"></span></h4>
                                <p class="list-group-item-text">VATUSA Competency Review and Certification for
                                    Approach</p>
                            </a>
                            <a class="list-group-item list-group-item list-group-item-success disabled" id="ctr">
                                <h4 class="list-group-item-heading">C1 (Center) Rating Review Form<span
                                        class="glyphicon glyphicon-arrow-right pull-right"></span></h4>
                                <p class="list-group-item-text">VATUSA Competency Review and Certification for
                                    Center</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
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

            $('#exam-date').datetimepicker({
              timepicker: false,
              format    : 'Y-m-d',
              mask      : true,
              maxDate   : '+1970/01/01',
              step      : 15
            })

            $('#position').keyup(function () {
              $(this).val($(this).val().toUpperCase())
              $('.list-group-item:not(.disabled)').addClass('disabled')
              let pos = $(this).val().split('_')
              if (pos.length)
                $('#' + pos.pop().toLowerCase()).removeClass('disabled')
            })
          })
        </script>
    @endpush
@endsection