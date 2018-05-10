@extends('layout')
@section('title', 'Controller Promotion')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Promotion Submission
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form class="form-horizontal" method="post" action="/mgt/controller/{{$u->cid}}/promote">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <b>Student</b>
                            </div>
                            <div class="col-sm-10">
                                {{$u->fname}} {{$u->lname}} ({{$u->cid}})
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="examiner" class="col-sm-2 control-label">Examiner CID</label>
                            <div class="col-sm-10">
                                <input type="number" id="examiner" name="examiner"
                                       placeholder="CID" value="{{\Auth::user()->cid}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <b>Rating Grantor</b>
                            </div>
                            <div class="col-sm-10">
                                {{\Auth::user()->fname}} {{\Auth::user()->lname}} ({{\Auth::user()->cid}})
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <b>Date of Exam</b>
                            </div>
                            <div class="col-sm-10">
                                <select name="year">
                                    <?php
                                    for ($year = date("Y") - 1; $year <= date("Y"); $year++) {
                                        echo '<option' . (($year == date("Y")) ? ' selected="true"' : '') . '>' . $year . '</option>';
                                    }
                                    ?>
                                </select> - <select name="month">
                                    <?php
                                    for ($x = 1; $x != 13; $x++) {
                                        echo '<option' . (($x == date("n")) ? ' selected="true"' : '') . ' value="' . $x . '"">' . date('M', mktime(0, 0, 0, $x, 1)) . '</option>';
                                    }
                                    ?></select> - <select name="day">
                                    <?php
                                    for ($x = 1; $x != 32; $x++) {
                                        echo '<option' . (($x == date("j")) ? ' selected="true"' : '') . '>' . (($x < 10) ? "0$x" : $x) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2 control-label">
                                <b>Exam Position</b>
                            </div>
                            <div class="col-sm-10">
                                <input type="text" name="position" id="position" placeholder="Position (ABC_CTR)">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2 text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[name=examiner]').autocomplete({
                source: '/ajax/cid',
                minLength: 2,
                select: function (event, ui) {
                    $('[name=examiner]').val(ui.item.value);

                    return false;
                }
            });
        });
    </script>
@endsection