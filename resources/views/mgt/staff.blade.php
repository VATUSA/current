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
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US1\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA2</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US2")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US2")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US2")!="Vacant")?
                                "<button onClick=\"deleteStaff('US2')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US2\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA3</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US3")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US3")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US3")!="Vacant")?
                                "<button onClick=\"deleteStaff('US3')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US3\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA4</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US4")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US4")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US4")!="Vacant")?
                                "<button onClick=\"deleteStaff('US4')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US4\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA5</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US5")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US5")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US5")!="Vacant")?
                                "<button onClick=\"deleteStaff('US5')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US5\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA6</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US6")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US6")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US6")!="Vacant")?
                                "<button onClick=\"deleteStaff('US6')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US6\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA7</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US7")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US7")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US7")!="Vacant")?
                                "<button onClick=\"deleteStaff('US7')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US7\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA8</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US8")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US8")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US8")!="Vacant")?
                                "<button onClick=\"deleteStaff('US8')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US8\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA9</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US9")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US9")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US9")!="Vacant")?
                                "<button onClick=\"deleteStaff('US9')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US9\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <!--
                        <tr>
                            <td>VATUSA10</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US10")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US10")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US10")!="Vacant")?
                                "<button onClick=\"deleteStaff('US10')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US10\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA11</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US11")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US11")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US11")!="Vacant")?
                                "<button onClick=\"deleteStaff('US11')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US11\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA12</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US12")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US12")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US12")!="Vacant")?
                                "<button onClick=\"deleteStaff('US12')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US12\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA13</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US13")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US13")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US13")!="Vacant")?
                                "<button onClick=\"deleteStaff('US13')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US13\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA14</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US14")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US14")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US14")!="Vacant")?
                                "<button onClick=\"deleteStaff('US14')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US14\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        <tr>
                            <td>VATUSA15</td>
                            <td>{{\App\Classes\RoleHelper::getNameFromRole("US15")}}</td>
                            <td>{{\App\Classes\RoleHelper::roleTitle("US15")}}</td>
                            <td>{!!((\App\Classes\RoleHelper::getNameFromRole("US15")!="Vacant")?
                                "<button onClick=\"deleteStaff('US15')\" class=\"btn btn-danger\">Vacate</button>"
                                :"<button data-toggle=\"modal\" data-target=\"#staffModal\" data-role=\"US15\" class=\"btn btn-success\">Assign</button>")!!}</td>
                        </tr>
                        -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="staffModal" tabindex="-1" role="dialog" aria-labelledby="staffModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="staffModalTitle">Assign Staff</h5>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to assign this VATUSA staff position? If so, please search or enter a CID
                        for the controller you wish to assign VAT<span id="roleName"></span> to.</p>
                    <hr>
                    <input type="hidden" id="roleHidden">
                    <label for="cid">CID or Last Name:</label>
                    <input type="text" name="cid" class="form-control" id="cidsearch">
                    Transfer to ZHQ? <span id="toggleTransfer" style="font-size:1.8em;margin-left: 20px;">
                                <i class="toggle-icon fa fa-toggle-on text-success"></i>
                                <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                    </span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="assignButton" class="btn btn-sm btn-success">Assign</button>
                </div>

            </div>
        </div>
    </div>
    <script>
      $('#toggleTransfer').click(function () {
        let icon        = $(this).find('i.toggle-icon'),
            currentlyOn = icon.hasClass('fa-toggle-on')
            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
              ' text-' + (currentlyOn ? 'danger' : 'success'))
      })
    </script>
    <script type="text/javascript">
      function deleteStaff (role) {
        roletext = role.replace('US', 'USA')
        bootbox.confirm('Vacate the ' + roletext + ' position?', function (result) {
          if (result === true) {
            $.ajax({
              url : '/mgt/staff/' + role,
              type: 'DELETE'
            }).success(function () {
              location.reload()
            }).error(function () { alert('Error occurred') })
          }
        })
      }

      $('#staffModal').on('shown.bs.modal', function (event) {
        // Setting Values
        var role_str = $(event.relatedTarget).data('role')
        var role = role_str.replace('US', 'USA')

        $('#roleName').html(role)
        $('#roleHidden').val(role_str)
      })

      $('#assignButton').click(function () {
        // Setting Values
        var cid = $('#cidsearch').val()
        var role = $('#roleHidden').val()

        $.ajax({
          url : '/mgt/staff/' + role,
          type: 'PUT',
          data: {
            cid: cid,
            xfer: $('#toggleTransfer').find('i.toggle-icon').hasClass('fa-toggle-on')
          }
        }).success(function (res) {
          location.reload(true)
        }).error(function () {
          alert('Error occurred')
        })
      })

      $('#cidsearch').devbridgeAutocomplete({
        lookup  : [],
        onSelect: (suggestion) => {
          $('#cidsearch').val(suggestion.data)
        }
      })

      var prevVal = ''

      $('#cidsearch').on('change keydown keyup paste', function () {
        let newVal = $(this).val()
        if (newVal.length === 4 && newVal !== prevVal) {
          let url = '/v2/user/' + (isNaN(newVal) ? 'filterlname/' : 'filtercid/')
          prevVal = newVal
          $.get($.apiUrl() + url + newVal)
            .success((data) => {
              $('#cidsearch').devbridgeAutocomplete().setOptions({
                lookup: $.map(data.data, (item) => {
                  return {value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid}
                })
              })
              $('#cidsearch').focus()
            })
        }
      })

    </script>
@stop
