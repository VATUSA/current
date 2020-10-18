@extends('layout')
@section('title', 'Members & Staff')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Members &amp; Staff
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#staff" aria-controls="staff" role="tab"
                                                                  data-toggle="tab">VATUSA Staff</a></li>
                        <li role="presentation"><a href="#mem" aria-controls="mem" role="tab" data-toggle="tab">VATUSA
                                Members</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="staff">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Name</th>
                                    <th>Position Description</th>
                                    <th>Email</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>VATUSA1</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US1")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US1")}}</td>
                                    <td><a href="mailto:vatusa1@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA12</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US12")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US12")}}</td>
                                    <td><a href="mailto:vatusa12@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA2</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US2")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US2")}}</td>
                                    <td><a href="mailto:vatusa2@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA3</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US3")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US3")}}</td>
                                    <td><a href="mailto:vatusa3@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA13</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US13")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US13")}}</td>
                                    <td><a href="mailto:vatusa13@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA4</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US4")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US4")}}</td>
                                    <td><a href="mailto:vatusa4@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA14</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US14")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US14")}}</td>
                                    <td><a href="mailto:vatusa14@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA5</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US5")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US5")}}</td>
                                    <td><a href="mailto:vatusa5@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA15</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US15")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US15")}}</td>
                                    <td><a href="mailto:vatusa15@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA25</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US25")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US25")}}</td>
                                    <td><a href="mailto:vatusa25@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA6</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US6")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US6")}}</td>
                                    <td><a href="mailto:vatusa6@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA7</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US7")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US7")}}</td>
                                    <td><a href="mailto:vatusa7@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA8</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US8")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US8")}}</td>
                                    <td><a href="mailto:vatusa8@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA9</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US9")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US9")}}</td>
                                    <td><a href="mailto:vatusa9@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA10</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US10")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US10")}}</td>
                                    <td><a href="mailto:vatusa10@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                <tr>
                                    <td>VATUSA11</td>
                                    <td>{{\App\Classes\RoleHelper::getNameFromRole("US11")}}</td>
                                    <td>{{\App\Classes\RoleHelper::roleTitle("US11")}}</td>
                                    <td><a href="mailto:vatusa11@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="mem"><br>
                            <form class="form-horizontal artcc-mem-search">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Select a Facility</label>

                                    <div class="col-sm-6">
                                        <select class="form-control" id="facility">
                                            <option value="0">Select a Facility</option>
                                            <optgroup label="Western Region">
                                                @foreach (\App\Facility::where(['active' => 1, 'region' => 7])->orderBy('name')->get() as $facility)
                                                    <option value="{{$facility->id}}">{{$facility->name}}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Southern Region">
                                                @foreach (\App\Facility::where(['active' => 1, 'region' => 8])->orderBy('name')->get() as $facility)
                                                    <option value="{{$facility->id}}">{{$facility->name}}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Northeastern Region">
                                                @foreach (\App\Facility::where(['active' => 1, 'region' => 9])->orderBy('name')->get() as $facility)
                                                    <option value="{{$facility->id}}">{{$facility->name}}</option>
                                                @endforeach
                                            </optgroup>

                                        </select>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div id="fac_info">
                            <center>Select a facility</center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#facility").change(function () {
                if ($('#facility').val() == 0) { $('#fac_info').html("<center>Select a facility</center>"); return; }
                document.getElementById('fac_info').style.visibility = 'visible';
                document.getElementById('fac_info').innerHTML = '<i class="fa fa-refresh fa-spin"></i>';

                jQuery.ajax({
                    type: "POST",
                    url: '/info/ajax/members',
                    data: {fac: $("#facility").val()},

                    success: function (obj) {
                        document.getElementById('fac_info').innerHTML = obj;
                    }
                });

            });
        });
    </script>
@stop
