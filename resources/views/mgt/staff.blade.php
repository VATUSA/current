@extends('layout')
@section('title', 'Division Staff Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Division Staff Management
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Position</th>
                            <th>Name</th>
                            <th>Position Description</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>VATUSA1</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US1")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US1")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US1")!="Vacant")?
                                "<button onClick=\"deleteStaff('US1')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US1')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA2</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US2")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US2")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US2")!="Vacant")?
                                "<button onClick=\"deleteStaff('US2')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US2')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA3</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US3")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US3")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US3")!="Vacant")?
                                "<button onClick=\"deleteStaff('US3')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US3')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA13</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US13")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US13")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US13")!="Vacant")?
                                "<button onClick=\"deleteStaff('US13')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US13')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA4</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US4")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US4")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US4")!="Vacant")?
                                "<button onClick=\"deleteStaff('US4')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US4')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA14</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US14")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US14")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US14")!="Vacant")?
                                "<button onClick=\"deleteStaff('US14')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US14')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA5</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US5")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US5")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US5")!="Vacant")?
                                "<button onClick=\"deleteStaff('US5')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US5')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA6</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US6")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US6")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US6")!="Vacant")?
                                "<button onClick=\"deleteStaff('US6')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US6')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA7</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US7")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US7")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US7")!="Vacant")?
                                "<button onClick=\"deleteStaff('US7')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US7')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA8</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US8")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US8")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US8")!="Vacant")?
                                "<button onClick=\"deleteStaff('US8')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US8')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA9</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US9")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US9")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US9")!="Vacant")?
                                "<button onClick=\"deleteStaff('US9')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US9')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA10</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US10")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US10")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US10")!="Vacant")?
                                "<button onClick=\"deleteStaff('US10')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US10')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA11</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US11")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US11")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US11")!="Vacant")?
                                "<button onClick=\"deleteStaff('US11')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button onClick=\"assignStaff('US11')\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script>
        function deleteStaff(role) {
            roletext = role.replace("US","USA");
            bootbox.confirm("Vacate the " + roletext + " position?", function (result) {
                if (result === true) {
                    $.ajax({
                        url: "/mgt/staff/" + role,
                        type: "DELETE"
                    }).success(function() {
                        location.reload();
                    }).error(function() { alert("Error occurred"); });
                }
            });
        }

        function assignStaff(role) {
            roletext = role.replace("US","USA");
            bootbox.prompt("Who do you want to assign to " + roletext + "?", function (result) {
                if (result === null) return;

                $.ajax({
                    url: "/mgt/staff/" + role,
                    type: "PUT",
                    data: { cid: result }
                }).success(function(res) { location.reload(); })
                .error(function() { alert("Error occurred"); });
            });
        }
    </script>
@stop
