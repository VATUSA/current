@extends('layout')
@section('title', 'Facility Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(!\App\Classes\RoleHelper::isMentor() || (\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor()))
                    <select id="facmgt"
                            class="mgt-sel">@foreach(\App\Facility::where('active', 1)->orderby('id', 'ASC')->get() as $f)
                            <option name="{{$f->id}}"
                                    @if($f->id == $fac) selected @endif>{{$f->id}}</option> @endforeach</select> - @endif
                    Facility Management
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#dash" aria-controls="dash" role="tab"
                                                                  data-toggle="tab">Dashboard</a></li>
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#trans" aria-controls="trans" role="tab" data-toggle="tab">Transfers</a>
                            </li>
                        @endif
                        <li role="presentation"><a href="#mem" aria-controls="mem" role="tab"
                                                   data-toggle="tab">Members</a></li>
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#uls" aria-controls="uls" role="tab" data-toggle="tab">Tech
                                    Conf</a>
                            </li>
                        @endif
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">Email
                                    Conf</a>
                            </li>
                            @if($facility->hosted_email_domain != "")
                            <li role="presentation"><a href="#hosted" aria-controls="hosted" role="tab" data-toggle="tab">Hosted Email
                                    Conf</a>
                            </li>
                            @endif
                        @endif
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#emailtemplates" aria-controls="emailtemplates" role="tab" data-toggle="tab">Email Templates</a></li>
                        @endif
                    <!--<li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>-->
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="dash">
                            <table class="fac-dash">
                                <tr>
                                    <td style="width: 33%"><h1><span data-toggle="tooltip" data-placement="bottom"
                                                                     title="Total Controllers"><i
                                                        class="fa fa-user"></i> {{\App\User::where('facility',$fac)->count()}}</span>
                                        </h1></td>
                                    <td style="width: 34%"><h1><span data-toggle="tooltip" data-placement="bottom"
                                                                     title="Pending Transfers"><i
                                                        class="fa fa-user-plus"></i> {{\App\Transfers::where('to', $fac)->where('status', 0)->count()}}</span>
                                        </h1></td>
                                    <td><h1><span data-toggle="tooltip" data-placement="bottom"
                                                  title="Promotions this Month"><i
                                                        class="fa fa-star"></i> {{$promotionEligible}}</span></h1></td>
                                </tr>
                            </table>
                            <hr>
                            <h4>Facility Staff Administration</h4>
                            <div id="staff-table"></div>
                        </div>
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="trans">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>CID</th>
                                        <th>Name</th>
                                        <th>Rating</th>
                                        <th>Options</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $x = 0;?>
                                    @foreach(\App\Transfers::where('to', $fac)->where('status', 0)->get() as $t)
                                        <?php
                                        $user = App\User::where('cid', $t->cid)->first();
                                        $x = 1;
                                        ?>
                                        <tr id="trans{{$t->id}}">
                                            <td>{{$t->created_at}}</td>
                                            <td>{{$user->cid}}</td>
                                            <td>{{$user->fname}} {{$user->lname}}</td>
                                            <td>{{$user->urating->short}}</td>
                                            @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff())
                                                <td><a href="/mgt/controller/{{$t->cid}}"><i
                                                                class="fa fa-search"></i></a> &nbsp; <a href="#"
                                                                                                        onClick="appvTrans({{$t->id}})"><i
                                                                class="fa fa-check"></i></a> &nbsp; <a href="#"
                                                                                                       onClick="rejTrans({{$t->id}})"><i
                                                                class="fa fa-remove"></i></a></td>
                                            @else
                                                <td>&nbsp;</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    @if ($x == 0)
                                        <tr>
                                            <td colspan="5">
                                                <center>No pending transfers.</center>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div role="tabpanel" class="tab-pane" id="mem">
                            <div id="memloading"><center><img src="/img/gears.gif"><br><br>Loading members table...</center></div>
                            <table class="table table-hover table-condensed tablesorter" id="memtable" style="display: none;">
                                <thead>
                                <tr>
                                    <th>CID</th>
                                    <th>Name</th>
                                    <th>Rating</th>
                                    <th>Join Date</th>
                                    <td class="text-right">Options</td>
                                </tr>
                                </thead>
                                <tbody id="memtablebody">
                                </tbody>
                            </table>
                        </div>
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="uls">
                                <b>Website URL:</b>
                                <input type="text" id="facurl" class="form-control" value="{{$facility->url}}" />
                                <button class="btn btn-primary" onClick="updateUrl()">Update</button>
                                <h1>ULS</h1>
                                <b>ULSv2 JSON Web Key (JWK):</b> (<a href="https://tools.ietf.org/html/rfc7515">RFC7515</a> page 38) -- symmetric key<br>
                                <input type="text" readonly id="textulsv2jwk" class="form-control" value="{{$facility->uls_jwk}}"><br>
                                <button class="btn btn-primary" onClick="ulsv2JWK()">Generate New</button>
                                <br><br>
                                <b>ULSv1 Key:</b> <u>DEPRECATED (Removal 5/31/18)</u><br>
                                <input class="form-control" type="text" id="key" value="{{$facility->uls_secret}}"><br>
                                <button class="btn btn-primary" onClick="ulsGen()">Generate New</button>
                                <br><br>
                                <b>Return URL:</b><br><input class="form-control" type="text" id="ret" value="{{$facility->uls_return}}"><br>
                                <button class="btn btn-primary" onClick="ulsUpdate()">Update</button>
                                <br><br>
                                <b>Dev Environment Return URL:</b><br><input class="form-control" type="text" id="devret" value="{{$facility->uls_devreturn}}"><br>
                                <button class="btn btn-primary" onClick="ulsDevUpdate()">Update</button>
                                <br><br>
                                <h1>API (v1/v2)</h1>
                                <b>APIv2 JWK:</b> (<a href="https://tools.ietf.org/html/rfc7515">RFC7515</a> page 38) -- symmetric key<br>
                                <input class="form-control" type="text" id="textapiv2jwk" value="{{$facility->apiv2_jwk}}" readonly><br>
                                <button class="btn btn-primary" onClick="apiv2JWK()">Generate New</button>
                                <br><br>
                                <b>API Key:</b><br><input class="form-control" type="text" id="apikey" value="{{$facility->apikey}}"><br>
                                <button class="btn btn-primary" onClick="apiGen()">Generate New</button>
                                <br><br>
                                <b>Sandbox API Key:</b><br><input class="form-control" type="text" id="apisbkey" value="{{$facility->api_sandbox_key}}"><br>
                                <button class="btn btn-primary" onClick="apiSBGen()">Generate New</button>
                                <br><br>
                                @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                                    <b>IP (v1 only):</b><br><input class="form-control" type="text" id="apiip" value="{{$facility->ip}}"><br>
                                    <button class="btn btn-primary" onClick="ipUpdate()">Update</button>
                                    <br><br>
                                    <b>Sandbox IP (v1 only):</b><br><input class="form-control" type="text" id="apisbip" value="{{$facility->api_sandbox_ip}}"><br>
                                    <button class="btn btn-primary" onClick="ipSBUpdate()">Update</button>
                                @else
                                    <b>IP (v1 only):</b> {{$facility->ip}}<br>
                                    <b>Sandbox IP (v1 only):</b> {{$facility->api_sandbox_ip}}
                                @endif
                            </div>
                        @endif
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="email">
                                <select class="form-control" id="facilityEmail">
                                    <option value=0>Select an address</option>
                                    @foreach(['atm','datm','ta','ec','fe','wm'] as $role)
                                        <option value="{{$role}}">{{strtolower($fac)}}-{{$role}}@vatusa.net</option>
                                    @endforeach
                                </select>
                                <div id="emailBox" style="display: none;">
                                    <div class="form-group">
                                        <label class="control-label">Destination (separate multiple addresses with a comma)</label>
                                        <input class="form-control" type="text" id="emailDestination" placeholder="Destination email address">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Static?</label>
                                        <select class="form-control" id="emailStatic">
                                            <option value="1">Yes</option><option value="0">No</option>
                                        </select>
                                    </div>
                                    <button class="btnEmailSave btn btn-primary">Save</button>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="hosted">
                                <div id="ehloading"><center><img src="/img/gears.gif"><br><br>Loading hosted emails table...</center></div>
                                <table class="table table-bordered" id="ehtable" style="display: none;">
                                    <thead>
                                        <tr>
                                            <th>Email Username (before @)</th>
                                            <th>Associated CID</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td><input class="form-control" type="test" id="nhemail" placeholder="New Address (before @ only)"></td>
                                            <td><input class="form-control" type="number" id="nhcid" placeholder="CERT ID"></td>
                                            <td><button class="btn btn-primary nhbtn">Add Account</button></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="emailtemplates">
                                <select class="form-control" id="facilityEmailTemplate">
                                    <option value=0>Select Template</option>
                                    <option value="examassigned">Exam Assigned</option>
                                    <option value="examfailed">Exam Failed</option>
                                    <option value="exampassed">Exam Passed</option>
                                    <option value="welcome">Welcome</option>
                                </select>
                                <div id="emailTemplateBox" style="display: none;">
                                    <div class="form-group">
                                        <label class="control-label">Body</label>
                                        <textarea rows="15" class="form-control" type="text" id="emailTemplateBody"></textarea>
                                    </div>
                                    <button class="btnEmailTemplateSave btn btn-primary">Save</button>
                                    <br><br>
                                    Variables (used by doing &#123;&#123;variable&#125;&#125;, ie, &#123;&#123;$fname&#125;&#125;):<br>
                                    <ul id="emailTemplateVariableList">

                                    </ul><br>
                                    You can use blade template methods, documentation found <a href="https://laravel.com/docs/5.5/blade">here</a>. <b>PHP code is not authorized.</b>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $.post('/mgt/ajax/staff/{{$fac}}', function (data) {
                $("#staff-table").html(data);
            });
            $("#facmgt").change(function () {
                window.location = "/mgt/facility/" + $("#facmgt").val();
            });
            var hash = document.location.hash;
            if (hash)
                $('.nav-tabs a[href=' + hash + ']').tab('show');

            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });

            $.ajax({
                url: $.apiUrl() + '/roster-{{$fac}}',
                type: "GET"
            }).success(function(data) {
                data = JSON.parse(data);
                var html = "";
                $.each(data.users, function(i) {
                    html = html + "<tr><td>" + data.users[i].cid + "</td>";
                    html = html + "<td>" + data.users[i].lname + ", " + data.users[i].fname + "</td>";
                    html = html + "<td>" + data.users[i].rating_short + "</td>";
                    var date = new Date(data.users[i].join_date);
                    html = html + "<td>" + (date.getMonth()+1) + "/" + date.getDate() + "/" + date.getFullYear() + "</td>";
                    html = html + "<td class=\"text-right\">";
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isInstructor(\Auth::user()->cid, $fac))
                        if (data.users[i].promotion_eligible == "1") {
                            html = html + "<a href=\"/mgt/controller/" + data.users[i].cid + "/promote\"><i class=\"text-yellow fa fa-star\"></i></a> &nbsp; ";
                        }
                    @endif
                    html = html + "<a href=\"/mgt/controller/" + data.users[i].cid + "\"><i class=\"fa fa-search\"></i></a>";
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff())
                        html = html + " &nbsp; <a href=\"#\" onClick=\"deleteController(" + data.users[i].cid + ")\"><i class=\"text-danger fa fa-remove\"></i></a>";
                    @endif
                    html = html + "</td></tr>";
                });
                $('#memtablebody').html(html);
                $('#memtable').toggle();
                $('#memloading').toggle();
                $('#memtable').tablesorter();
            });
        });
        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac, false))
                @if($facility->hosted_email_domain != "")
        const loadHostedEmailTable = () => {
          $("#ehtable").hide();
          $("#ehloading").show();
          $("#nhemail").val("");
          $("#nhcid").val("");
          $.ajax({
            url: `${$.apiUrl()}/v2/email/hosted?facility={{$fac}}`,
            method: 'GET',
            dataType: 'JSON'
          }).done((r) => {
            let html;
            console.dir(r);
            $.each(r.emails, (i) => {
              html = `${html}
                <tr><td style="vertical-align: middle">${ r.emails[i].username }</td><td style="vertical-align: middle">${ r.emails[i].cid }</td><td><button class="btn btn-danger nhDelete" data-username="${ r.emails[i].username }">Delete</button></td></tr>
              `;
            });
            $("#ehloading").hide();
            $("#ehtable > tbody").html(html);
            $("#ehtable").show();
          }).fail((r) => {
            $("#ehloading").hide();
            $("#ehtable > tbody").html(`<tr><td colspan="3"><center>There was an error processing this request.</center></td></tr>`);
          })
        }
        $(document).ready(() => loadHostedEmailTable());
        $(document).on("click", ".nhDelete", (e) => {
          $("#ehloading").show();
          $("#ehtable").hide();
          $.ajax({
            method: "DELETE",
            url: `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${ $(e.currentTarget).data("username") }`
          })
          .done(() => loadHostedEmailTable())
          .fail((r) => {
            bootbox.alert("There was an error deleting this email.");
            loadHostedEmailTable();
          });
        });
        $(document).on('click','.nhbtn', () => {
          let check = new RegExp('^[a-zA-Z0-9_-]+$');
          if (!check.test($("#nhemail").val())) {
            bootbox.alert("Invalid characters in username box. Only include the portion before the @ in an email address.");
            return;
          }
          const email = $("#nhemail").val();
          check = new RegExp('^[0-9]{6,}');
          if (!check.test($("#nhcid").val())) {
            bootbox.alert("Invalid CERT ID");
            return;
          }
          const cid = $("#nhcid").val();
          $.ajax({
            method: "POST",
            url: `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${email}`,
            data: { cid }
          }).done(() => {
            loadHostedEmailTable();
          }).fail((r) => {
            if (r.status === 401) {
              bootbox.alert("Got an unauthenticated error. Please try logging in again.");
              return;
            }
            if (r.status === 403) {
              bootbox.alert("Access denied.");
              return;
            }
            if (r.status === 404) {
              bootbox.alert("Got a not found error. Please check the CID, they must be known to the VATUSA system.");
              return;
            }
            bootbox.alert("Got an unknown error: " + JSON.stringify(r));
          });
        });
            @endif
        $(document).on('change','#facilityEmail',function() {
          if ($('#facilityEmail').val() == 0) {
            return;
          }
          waitingDialog.show();
          $('#emailBox').hide();
          $.ajax({
            method: 'GET',
            url: `${$.apiUrl()}/v2/email/{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`,
            dataType: 'json',
            xhrFields: {
              includeCredentials: true
            }
          }).done((data) => {
            waitingDialog.hide();
            if (data.type === "STATIC") {
              $('#emailStatic').val('1');
            } else {
              $('#emailStatic').val('0');
            }
            $('#emailDestination').val(data.destination);
            $('#emailBox').show();
          }).fail((data) => {
            waitingDialog.hide();
            bootbox.alert(`Problem handling this request ${data.msg}`);
          });
        }).on('click','.btnEmailSave', function() {
          waitingDialog.show();
          $.ajax({
            method: 'POST',
            url: `${$.apiUrl()}/v2/email`,
            data: { email: `{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`, destination: $('#emailDestination').val(), static: ($('#emailStatic').val() == "1") ? true : false }
          }).done((data) => {
            waitingDialog.hide();
            bootbox.alert("Changes have been saved.");
          }).fail((data) => {
            waitingDialog.hide();
            bootbox.alert(`There was an error processing the request.  Server said: ${data.msg}`);
          });
        });
        @endif
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
        $(document).on('change', '#facilityEmailTemplate', () => {
          if ($("#facilityEmailTemplate").val() == "welcome") {
            window.location="/mgt/mail/welcome";
            return;
          }
          waitingDialog.show();
          $.ajax({
            url: `${ $.apiUrl() }/v2/facility/{{$fac}}/email/${ $("#facilityEmailTemplate").val() }`,
            method: "GET"
          }).done((data) => {
            $("#emailTemplateBody").val(data.body)
            waitingDialog.hide();
            $("#emailTemplateBox").show();
            $("#emailTemplateVariableList").html("");
            for (let variable of data.variables) {
              $("#emailTemplateVariableList").append(`<li>$${ variable }</li>`);
            }
          }).fail((data) => {
            waitingDialog.hide();
            bootbox.alert("Failed to load email template from API, got: " + data)
          })
        }).on('click', '.btnEmailTemplateSave', () => {
          waitingDialog.show();
          $.ajax({
            url: `${ $.apiUrl() }/v2/facility/{{$fac}}/email/${ $("#facilityEmailTemplate").val()}`,
            method: "POST",
            data: { body: $("#emailTemplateBody").val() }
          }).done(() => {
            bootbox.alert("Template saved successfully.");
            waitingDialog.hide();
          }).fail((data) => {
            bootbox.alert(`Template save failed`);
            waitingDialog.hide();
          })
        });
        @endif
        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))

        function updateUrl() {
          $.ajax(
              { method: "put", url: $.apiUrl() + "/v2/facility/{{$fac}}", data: { url: $('#facurl').val() }}
          ).done(function(result) {
            bootbox.alert("URL saved successfully");
          }).fail(function (result) {
            bootbox.alert("URL save failed.");
          });
        }
        function ulsv2JWK() {
          $.ajax(
            { method: "put", url: $.apiUrl() + "/v2/facility/{{$fac}}", data: { uls2jwk: '' }}
          ).done(function (result) {
            if (result) $('#textulsv2jwk').val(JSON.stringify(result));
          });
        }
        function apiv2JWK() {
          $.ajax(
            { method: "put", url: $.apiUrl() + "/v2/facility/{{$fac}}", data: { apiv2jwk: '' }}
          ).done(function (result) {
            if (result) $('#textapiv2jwk').val(JSON.stringify(result));
          });
        }
        function ulsGen() {
            $.post("/mgt/facility/{{$fac}}/uls/generate", function (result) {
                if (result) $('#key').attr('value', result);
            });
        }
        function ulsUpdate() {
            $.post("/mgt/facility/{{$fac}}/uls/return", { ret: $('#ret').val() }).done(function (result) {
                bootbox.alert("Updated");
            });
        }
        function ulsDevUpdate() {
            $.post("/mgt/facility/{{$fac}}/uls/devreturn", { ret: $('#devret').val() }).done(function (result) {
                bootbox.alert("Updated");
            });
        }
        function apiGen() {
            $.post("/mgt/facility/{{$fac}}/api/generate", function (result) {
                if (result) $('#apikey').attr('value', result);
            });
        }
        function apiSBGen() {
            $.post("/mgt/facility/{{$fac}}/api/generate/sandbox", function (result) {
                if (result) $('#apisbkey').attr('value', result);
            });
        }
        @endif
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
        function ipUpdate() {
            $.post("/mgt/facility/{{$fac}}/api/update", { apiip: $('#apiip').val() }).done(function (result) {
                if (result == 1) bootbox.alert("Updated");
            });
        }

        function ipSBUpdate() {
            $.post("/mgt/facility/{{$fac}}/api/update/sandbox", { apiip: $('#apisbip').val() }).done(function (result) {
                if (result == 1) bootbox.alert("Updated");
            });
        }

        function appvTrans(id) {
            bootbox.confirm("Confirm approval?", function (result) {
                if (result) {
                    $.post("/mgt/ajax/transfers/1", {id: id}, function (data) {
                        bootbox.alert(data);
                        window.refresh();
                        location.reload(true);
                    });
                }
            });
        }
        function rejTrans(id) {
            bootbox.prompt("Reason for rejection:", function (result) {
                if (result === null) {
                } else {
                    $.post("/mgt/ajax/transfers/2", {id: id, reason: result}, function (data) {
                        bootbox.alert(data);
                        window.refresh();
                        location.reload(true);
                    });
                }
            });
        }
        function deleteController(cid) {
            bootbox.prompt("Reason for delete:", function (result) {
                if (result === null) {
                    return;
                } else {
                    $.ajax({
                        url: 'https://api.vatusa.net/v2/facility/{{$fac}}/roster/' + cid,
                        type: 'DELETE',
                        data: {'reason': result}
                    }).success(function () {
                        location.reload(true);
                    });
                }
            })
        }
        function posDel(val) {
            var val_lng;
            switch (val) {
                case 1:
                    val_lng = "ATM";
                    break;
                case 2:
                    val_lng = "DATM";
                    break;
                case 3:
                    val_lng = "TA";
                    break;
                case 4:
                    val_lng = "EC";
                    break;
                case 5:
                    val_lng = "FE";
                    break;
                case 6:
                    val_lng = "WM";
                    break;
            }
            bootbox.confirm("Confirm vacancy of " + val_lng + " ?", function (result) {
                if (result) {
                    $.post("{{secure_url('mgt/ajax/del/position/'.$fac)}}", {pos: val}, function (data) {
                        bootbox.alert(data);
                        $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                            $("#staff-table").html(data);
                        });
                    });
                }
            });
        }
        function posEdit(val) {
            switch (val) {
                case 1:
                    bootbox.prompt("Enter new CID for ATM", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/1')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });
                    break;
                case 2:
                    bootbox.prompt("Enter new CID for DATM", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/2')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });
                    break;
                case 3:
                    bootbox.prompt("Enter new CID for TA", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/3')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });
                    break;
                case 4:
                    bootbox.prompt("Enter new CID for EC", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/4')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });
                    break;
                case 5:
                    bootbox.prompt("Enter new CID for FE", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/5')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });
                    break;
                case 6:
                    bootbox.prompt("Enter new CID for WM", function (result) {
                        if (result === null) {
                        } else {
                            $.post("{{secure_url('mgt/ajax/position/'.$fac.'/6')}}", {cid: result}, function (data) {
                                bootbox.alert(data);
                                $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                    $("#staff-table").html(data);
                                });
                            });
                        }
                    });

                    break;
            }
        }
        @endif
    </script>
    <script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
@stop
