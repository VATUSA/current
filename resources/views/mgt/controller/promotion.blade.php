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
                                <div class="col-sm-3 control-label">
                                    <b>Student</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    {{$u->fname}} {{$u->lname}} ({{$u->cid}})
                                    <br>
                                    <!-- Show promotion graphic line in Ratings tab: -->
                                    <!-- S3 -> C1 -->
                                </p>
                            </div>
                            <div class="form-group">
                                <label for="examiner" class="col-sm-3 control-label">Examiner CID</label>
                                <div class="col-sm-9">
                                    <input type="number" id="examiner" name="examiner"
                                           placeholder="CID" value="{{\Auth::user()->cid}}" class="form-control"
                                           style="width:150px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Rating Grantor</b>
                                </div>
                                <p class="col-sm-9 form-control-static">
                                    {{\Auth::user()->fname}} {{\Auth::user()->lname}} ({{\Auth::user()->cid}})
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Date of Exam (UTC)</b>
                                </div>
                                <div class="col-sm-9">
                                    <input class="form-control" name="date" id="exam-date" value="{{ date('Y-m-d') }}"
                                           style="width:150px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3 control-label">
                                    <b>Exam Position</b>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" name="position" id="position" placeholder="Position (ABC_CTR)"
                                           class="form-control" style="width:150px;">
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
                                    <button type="submit" class="btn btn-success btn-block" style="width:150px;" disabled><span
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
          $(document).ready(function () {
            $('[name=examiner]').autocomplete({
              source   : '/ajax/cid',
              minLength: 2,
              select   : function (event, ui) {
                $('[name=examiner]').val(ui.item.value)

                return false
              }
            })
          })
        </script>
        <script type="text/javascript">
          $(function () {

          })
        </script>
    @endpush
@endsection