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
                                        @if($f->id == $fac) selected @endif>{{$f->id}}</option> @endforeach</select>
                        - @endif
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
                                <li role="presentation"><a href="#hosted" aria-controls="hosted" role="tab"
                                                           data-toggle="tab">Hosted Email
                                        Conf</a>
                                </li>
                            @endif
                        @endif
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#emailtemplates" aria-controls="emailtemplates" role="tab"
                                                       data-toggle="tab">Email Templates</a></li>
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
                            <div id="memloading">
                                <center><img src="/img/gears.gif"><br><br>Loading members table...</center>
                            </div>
                            <table class="table table-hover table-condensed tablesorter" id="memtable"
                                   style="display: none;">
                                <thead>
                                <tr>
                                    <th>CID</th>
                                    <th>Name</th>
                                    <th>Rating</th>
                                    <th>Join Date</th>
                                    <th>Last Promotion</th>
                                    <td class="text-right">Options</td>
                                </tr>
                                </thead>
                                <tbody id="memtablebody">
                                </tbody>
                            </table>
                        </div>
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="uls">
                                <br>
                                <b>Website URL:</b>
                                <input type="text" id="facurl" class="form-control" value="{{$facility->url}}"
                                       autocomplete="off"/>
                                <button class="btn btn-primary" onClick="updateUrl()">Update</button>
                                <br><br>
                                <b>Development Website URL(s):</b>
                                <p class="help-block">Multiple Dev URLs can be specified, seperated by a
                                    <strong>comma</strong>.</p>
                                <input type="text" id="facurldev" class="form-control" value="{{$facility->url_dev}}"
                                       autocomplete="off"/>
                                <button class="btn btn-primary" onClick="updateDevUrl()">Update</button>
                                <hr>
                                <h1>ULS</h1>
                                <fieldset>
                                    <legend>Live</legend>
                                    <b>ULSv2 JSON Web Key (JWK):</b> (<a
                                        href="https://tools.ietf.org/html/rfc7515">RFC7515</a> page 38) -- symmetric key<br>
                                    <input type="text" readonly id="textulsv2jwk" class="form-control"
                                           value="{{$facility->uls_jwk}}" autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="ulsv2JWK()">Generate New</button>
                                    <br><br>
                                    <b>Return URLs:</b>
                                    <div id="return-URLs">
                                        <table class="table table-striped" id="ulsreturn-table">
                                            <thead>
                                            <tr>
                                                <th style="color:#7a7a7a; width:40px;">ID</th>
                                                <th style="color:#7a7a7a; width:500px;">URL</th>
                                                <th style="color:#7a7a7a;">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(\App\Classes\ULSHelper::getReturnPaths($facility->id) as $p)
                                                <tr id="path-{{$p->order}}">
                                                    <td class="rp-order">{{ $p->order }}</td>
                                                    <td class="rp-url">{{ $p->url }}</td>
                                                    <td class="rp-actions">
                                                        <button class="btn btn-info"
                                                                onclick="editUlsReturn({{$p->order . ", '" . $p->url . "'"}})">
                                                            <i class="fa fa-pencil"></i></button>
                                                        <button class="btn btn-danger"
                                                                onclick="removeUlsReturn({{$p->order}})">
                                                            <i class="fa fa-remove"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <button class="btn btn-success" onclick="addUlsReturn()">Add Return URL</button>
                                    </div>
                                </fieldset>
                                <br>
                                <fieldset>
                                    <legend>Development</legend>
                                    <b>Sandbox ULSv2 JSON Web Key (JWK):</b> (<a
                                        href="https://tools.ietf.org/html/rfc7515">RFC7515</a> page 38) -- symmetric key<br>
                                    <p class="help-block">When the <strong>?test</strong> query string parameter is set,
                                        ULS will not redirect to VATSIM login. Instead, it will authenticate a test user
                                        with CID 999 and random rating and email. Additionally, the signature will be
                                        created according to the Sandbox JWK below.</p>
                                    <input type="text" readonly id="textulsv2jwkdev" class="form-control"
                                           value="{{$facility->uls_jwk_dev}}" autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="ulsv2JWK(true)">Generate New</button>
                                    <button class="btn btn-warning" onClick="clearDevULSv2JWK()">Clear</button>
                                </fieldset>
                                <br><br>
                                <h1>APIv2</h1>
                                <fieldset>
                                    <legend>Live</legend>
                                    <b>APIv2 JWK:</b> (<a href="https://tools.ietf.org/html/rfc7515">RFC7515</a> page
                                    38) --
                                    symmetric key<br>
                                    <input class="form-control" type="text" id="textapiv2jwk"
                                           value="{{$facility->apiv2_jwk}}" readonly autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="apiv2JWK()">Generate New</button>
                                    <br><br>
                                    <b>API Key:</b><br><input class="form-control" type="text" id="apikey"
                                                              value="{{$facility->apikey}}" autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="apiGen()">Generate New</button>
                                </fieldset>
                                <br>
                                <fieldset>
                                    <legend>Development</legend>
                                    <b>Sandbox APIv2 JWK:</b> (<a href="https://tools.ietf.org/html/rfc7515">RFC
                                        7515</a> page
                                    38) --
                                    symmetric key<br>
                                    <p class="help-block">Development Website URL must be set correctly in order for
                                        returned data to be formatted according to RFC 7515.</p>
                                    <input class="form-control" type="text" id="textapiv2jwkdev"
                                           value="{{$facility->apiv2_jwk_dev}}" readonly autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="apiv2JWK(true)">Generate New</button>
                                    <button class="btn btn-warning" onClick="clearDevAPIv2JWK()">Clear</button>
                                    <br><br>
                                    <b>Sandbox API Key:</b><br>
                                    <p class="help-block">Use this key to prevent the live database from being
                                        changed.</p>
                                    <input class="form-control" type="text" id="apisbkey"
                                           value="{{$facility->api_sandbox_key}}" autocomplete="off"><br>
                                    <button class="btn btn-primary" onClick="apiSBGen()">Generate New</button>
                                </fieldset>
                                <hr>
                                <h1>APIv1 (Deprecated)</h1>
                                <fieldset>
                                    <legend>Live</legend>
                                    @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                                        <b>IP (v1 only):</b><br><input class="form-control" type="text" id="apiip"
                                                                       value="{{$facility->ip}}" autocomplete="off">
                                        <br>
                                        <button class="btn btn-primary" onClick="ipUpdate()">Update</button>
                                    @else
                                        <b>IP (v1 only):</b> {{$facility->ip}}<br>
                                    @endif
                                </fieldset>
                                <br>
                                <fieldset>
                                    <legend>Development</legend>
                                    @if (\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                                        <b>Sandbox IP (v1 only):</b><br><input class="form-control" type="text"
                                                                               id="apisbip"
                                                                               autocomplete="off"
                                                                               value="{{$facility->api_sandbox_ip}}">
                                        <br>
                                        <button class="btn btn-primary" onClick="ipSBUpdate()">Update</button>
                                    @else
                                        <b>Sandbox IP (v1 only):</b> {{$facility->api_sandbox_ip}}
                                    @endif
                                </fieldset>
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
                                        <label class="control-label">Destination (separate multiple addresses with a
                                            comma)</label>
                                        <input class="form-control" type="text" id="emailDestination"
                                               placeholder="Destination email address">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Static?</label>
                                        <select class="form-control" id="emailStatic">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <button class="btnEmailSave btn btn-primary">Save</button>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="hosted">
                                <div id="ehloading">
                                    <center><img src="/img/gears.gif"><br><br>Loading hosted emails table...</center>
                                </div>
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
                                        <td><input class="form-control" type="test" id="nhemail"
                                                   placeholder="New Address (before @ only)"></td>
                                        <td><input class="form-control" type="number" id="nhcid" placeholder="CERT ID">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary nhbtn">Add Account</button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="emailtemplates">
                                <div class="alert alert-info" style="margin-top: 5px;"><i class="fa fa-info-circle"></i>
                                    This functionality is in development and has no effect on the actual email sent.
                                    Contact Data Services to change the email template.
                                </div>
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
                                        <textarea rows="15" class="form-control" type="text"
                                                  id="emailTemplateBody"></textarea>
                                    </div>
                                    <button class="btnEmailTemplateSave btn btn-primary">Save</button>
                                    <br><br>
                                    Variables (used by doing &#123;&#123;variable&#125;&#125;, ie, &#123;&#123;$fname&#125;&#125;):<br>
                                    <ul id="emailTemplateVariableList">

                                    </ul>
                                    <br>
                                    You can use blade template methods, documentation found <a
                                        href="https://laravel.com/docs/5.5/blade">here</a>. <b>PHP code is not
                                        authorized.</b>
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
        $.post('{{ secure_url("/mgt/ajax/staff/$fac") }}', function (data) {
          $('#staff-table').html(data)
        })
        $('#facmgt').change(function () {
          window.location = '/mgt/facility/' + $('#facmgt').val()
        })
        var hash = document.location.hash
        if (hash)
          $('.nav-tabs a[href=' + hash + ']').tab('show')

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
          window.location.hash = e.target.hash
        })

        $.ajax({
          url : $.apiUrl() + '/v2/facility/{{$fac}}/roster',
          type: 'GET'
        }).success(function (data) {
          var html = ''
          $.each(data, function (i) {
            if(data[i].cid == undefined) return;
            html += '<tr><td>' + data[i].cid + '</td>'
            html += "<td>"
            if (data[i].isMentor == true) html += '<span class=\'label label-danger role-label\'>MTR</span> '
            html += data[i].lname + ', ' + data[i].fname
            html += '</td>'
            html += '<td data-text="' + data[i].rating + '"><span style="display:none">' + String.fromCharCode(64 + parseInt(data[i].rating)) + '</span>' + data[i].rating_short;
            if (data[i].isSupIns == true) html += ' <span class=\'label label-danger role-label\'>INS</span>'
            html += '</td>'
            var date = new Date(data[i].facility_join)
            html += '<td>' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + '</td>'
            var last_promotion = data[i].last_promotion
            if (last_promotion) var promotion_date = new Date(last_promotion)
            if (promotion_date) html += '<td>' + (promotion_date.getMonth() + 1) + '/' + promotion_date.getDate() + '/' + promotion_date.getFullYear() + '</td>'
            else html += '<td><span class="text-muted">N/A</span></td>'
            html += '<td class="text-right">'
              @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isInstructor(\Auth::user()->cid, $fac))
              if (data[i].promotion_eligible == true) {
                html += '<a href="/mgt/controller/' + data[i].cid + '/promote"><i class="text-yellow fa fa-star"></i></a> &nbsp; '
              }
              @endif
                html += '<a href="/mgt/controller/' + data[i].cid + '"><i class="fa fa-search"></i></a>'
              @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff())
                html += ' &nbsp; <a href="#" onClick="deleteController(' + data[i].cid + ')"><i class="text-danger fa fa-remove"></i></a>'
              @endif
                html += '</td></tr>'
          })
          $('#memtablebody').html(html)
          $('#memtable').toggle()
          $('#memloading').toggle()
          $('#memtable').tablesorter()
        })
      })
          @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac, false))
          @if($facility->hosted_email_domain != "")
      const loadHostedEmailTable = () => {
        $('#ehtable').hide()
        $('#ehloading').show()
        $('#nhemail').val('')
        $('#nhcid').val('')
        $.ajax({
          url     : `${$.apiUrl()}/v2/email/hosted?facility={{$fac}}`,
          method  : 'GET',
          dataType: 'JSON'
        }).done((r) => {
          let html
          console.dir(r)
          $.each(r.emails, (i) => {
            html = `${html}
                <tr><td style="vertical-align: middle">${r.emails[i].username}</td><td style="vertical-align: middle">${r.emails[i].cid}</td><td><button class="btn btn-danger nhDelete" data-username="${r.emails[i].username}">Delete</button></td></tr>
              `
          })
          $('#ehloading').hide()
          $('#ehtable > tbody').html(html)
          $('#ehtable').show()
        }).fail((r) => {
          $('#ehloading').hide()
          $('#ehtable > tbody').html(`<tr><td colspan="3"><center>There was an error processing this request.</center></td></tr>`)
        })
      }
      $(document).ready(() => loadHostedEmailTable())
      $(document).on('click', '.nhDelete', (e) => {
        $('#ehloading').show()
        $('#ehtable').hide()
        $.ajax({
          method: 'DELETE',
          url   : `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${$(e.currentTarget).data('username')}`
        })
          .done(() => loadHostedEmailTable())
          .fail((r) => {
            bootbox.alert('There was an error deleting this email.')
            loadHostedEmailTable()
          })
      })
      $(document).on('click', '.nhbtn', () => {
        let check = new RegExp('^[a-zA-Z0-9_-]+$')
        if (!check.test($('#nhemail').val())) {
          bootbox.alert('Invalid characters in username box. Only include the portion before the @ in an email address.')
          return
        }
        const email = $('#nhemail').val()
        check = new RegExp('^[0-9]{6,}')
        if (!check.test($('#nhcid').val())) {
          bootbox.alert('Invalid CERT ID')
          return
        }
        const cid = $('#nhcid').val()
        $.ajax({
          method: 'POST',
          url   : `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${email}`,
          data  : {cid}
        }).done(() => {
          loadHostedEmailTable()
        }).fail((r) => {
          if (r.status === 401) {
            bootbox.alert('Got an unauthenticated error. Please try logging in again.')
            return
          }
          if (r.status === 403) {
            bootbox.alert('Access denied.')
            return
          }
          if (r.status === 404) {
            bootbox.alert('Got a not found error. Please check the CID, they must be known to the VATUSA system.')
            return
          }
          bootbox.alert('Got an unknown error: ' + JSON.stringify(r))
        })
      })
      @endif
      $(document).on('change', '#facilityEmail', function () {
        if ($('#facilityEmail').val() == 0) {
          return
        }
        waitingDialog.show()
        $('#emailBox').hide()
        $.ajax({
          method   : 'GET',
          url      : `${$.apiUrl()}/v2/email/{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`,
          dataType : 'json',
          xhrFields: {
            includeCredentials: true
          }
        }).done((data) => {
          waitingDialog.hide()
          if (data.type === 'STATIC') {
            $('#emailStatic').val('1')
          } else {
            $('#emailStatic').val('0')
          }
          $('#emailDestination').val(data.destination)
          $('#emailBox').show()
        }).fail((data) => {
          waitingDialog.hide()
          bootbox.alert(`Problem handling this request ${data.msg}`)
        })
      }).on('click', '.btnEmailSave', function () {
        waitingDialog.show()
        $.ajax({
          method: 'POST',
          url   : `${$.apiUrl()}/v2/email`,
          data  : {
            email      : `{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`,
            destination: $('#emailDestination').val(),
            static     : ($('#emailStatic').val() == '1') ? true : false
          }
        }).done((data) => {
          waitingDialog.hide()
          bootbox.alert('Changes have been saved.')
        }).fail((data) => {
          waitingDialog.hide()
          bootbox.alert(`There was an error processing the request.  Server said: ${data.msg}`)
        })
      })
      @endif
      @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
      $(document).on('change', '#facilityEmailTemplate', () => {
        if ($('#facilityEmailTemplate').val() == 'welcome') {
          window.location = '/mgt/mail/welcome'
          return
        }
        waitingDialog.show()
        $.ajax({
          url   : `${$.apiUrl()}/v2/facility/{{$fac}}/email/${$('#facilityEmailTemplate').val()}`,
          method: 'GET'
        }).done((data) => {
          $('#emailTemplateBody').val(data.body)
          waitingDialog.hide()
          $('#emailTemplateBox').show()
          $('#emailTemplateVariableList').html('')
          for (let variable of data.variables) {
            $('#emailTemplateVariableList').append(`<li>$${variable}</li>`)
          }
        }).fail((data) => {
          waitingDialog.hide()
          bootbox.alert('Failed to load email template from API, got: ' + data)
        })
      }).on('click', '.btnEmailTemplateSave', () => {
        waitingDialog.show()
        $.ajax({
          url   : `${$.apiUrl()}/v2/facility/{{$fac}}/email/${$('#facilityEmailTemplate').val()}`,
          method: 'POST',
          data  : {body: $('#emailTemplateBody').val()}
        }).done(() => {
          bootbox.alert('Template saved successfully.')
          waitingDialog.hide()
        }).fail((data) => {
          bootbox.alert(`Template save failed`)
          waitingDialog.hide()
        })
      })

      @endif
      @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))

      function updateUrl () {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {url: $('#facurl').val()}}
        ).done(function (result) {
          bootbox.alert('URL saved successfully')
        }).fail(function (result) {
          bootbox.alert('URL save failed. ' + result.responseJSON.msg + '.')
        })
      }

      function updateDevUrl () {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {url_dev: $('#facurldev').val()}}
        ).done(function (result) {
          bootbox.alert('Dev URL saved successfully.')
        }).fail(function (result) {
          bootbox.alert('Dev URL save failed. ' + result.responseJSON.msg + '.')
        })
      }

      function ulsv2JWK (isdev = false) {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {ulsV2jwk: '', jwkdev: isdev}}
        ).done(function (result) {
          if (result) {
            if (!isdev) $('#textulsv2jwk').val(result.uls_jwk)
            else $('#textulsv2jwkdev').val(result.uls_jwk_dev)
          }
        })
      }

      function clearDevULSv2JWK () {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {ulsV2jwk: 'X', jwkdev: true}}
        ).done(function (result) {
          if (result.hasOwnProperty('status') && result.status === 'OK') {
            $('#textulsv2jwkdev').val('')
          }
        })
      }

      function apiv2JWK (isdev = false) {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {apiV2jwk: '', jwkdev: isdev}}
        ).done(function (result) {
          if (result.hasOwnProperty('status') && result.status === 'OK') {
            if (!isdev) $('#textapiv2jwk').val(result.api_jwk)
            else $('#textapiv2jwkdev').val(result.api_jwk_dev)
          }
        })
      }

      function clearDevAPIv2JWK () {
        $.ajax(
          {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {apiV2jwk: 'X', jwkdev: true}}
        ).done(function (result) {
          if (result.hasOwnProperty('status') && result.status === 'OK') {
            $('#textapiv2jwkdev').val('')
          }
        })
      }

      function ulsGen () {
        $.post("/mgt/facility/{{$fac}}/uls/generate", function (result) {
          if (result) $('#key').attr('value', result)
        })
      }

      function removeUlsReturn (id) {
        bootbox.confirm({
          message : '<strong>Are you sure you want to remove the return path?</strong><br>This will shift all succeeding order IDs down.',
          buttons : {
            confirm: {
              label    : 'Yes, delete',
              className: 'btn-danger'
            },
            cancel : {
              label    : 'No, cancel',
              className: 'btn-default'
            }
          },
          callback: result => {
            if (result) {
              waitingDialog.show()
              $.ajax({
                url   : $.apiUrl() + '/v2/facility/{{$fac}}/ulsReturns/' + id,
                method: 'DELETE'
              }).done(() => {
                waitingDialog.hide()
                location.reload()
              }).error(data => {
                waitingDialog.hide()
                bootbox.alert('<div class=\'alert alert-danger\'><strong>There was an error processing the request.</strong></div>')
              })
            }
          }
        })
      }

      function editUlsReturn (id, url) {
        bootbox.prompt({
          title   : 'Editing return URL #' + id,
          value   : url,
          callback: newUrl => {
            if (!newUrl) return null
            waitingDialog.show()
            $.ajax({
              url   : $.apiUrl() + '/v2/facility/{{$fac}}/ulsReturns/' + id,
              method: 'PUT',
              data  : {url: url}
            }).done(() => {
              waitingDialog.hide()
              $('#path-' + id).find('.rp-url').text(newUrl)
            }).error(data => {
              waitingDialog.hide()
              bootbox.alert('<div class=\'alert alert-danger\'><strong>There was an error processing the request.</strong></div>')
            })
          }
        })
      }

      function addUlsReturn () {
        let lastOrder = parseInt($('#ulsreturn-table').find('.rp-order').last().text()),
            order     = lastOrder ? lastOrder + 1 : 1
        bootbox.prompt('New return URL #' + order, url => {
          if (!url) return null
          waitingDialog.show()
          $.ajax({
            url   : $.apiUrl() + '/v2/facility/{{$fac}}/ulsReturns',
            method: 'POST',
            data  : {url: url, order: order}
          }).done(() => {
            waitingDialog.hide()
            let element = '<tr id="path-"' + order + '</td>' +
              '<td class="rp-order">' + order + '</td>' +
              '<td class="rp-url">' + url + '</td>' +
              '<td class="rp-actions">' +
              '<button class="btn btn-info" onclick="editUlsReturn(' + order + ')"><i class="fa fa-pencil"></i></button>' +
              '&nbsp;<button class="btn btn-danger" onclick="removeUlsReturn(' + order + ')"> <i class="fa fa-remove"></i></button>' +
              '</td>'
            $(element).appendTo('#ulsreturn-table > tbody')
          }).error(data => {
            waitingDialog.hide()
            bootbox.alert('<div class=\'alert alert-danger\'><strong>There was an error processing the request.</strong></div>')
          })
        })
      }

      function ulsDevUpdate () {
        $.post('{{ secure_url("/mgt/facility/$fac/uls/devreturn") }}', {ret: $('#devret').val()}).done(function (result) {
          bootbox.alert('Updated')
        })
      }

      function apiGen () {
        $.post('{{ secure_url("/mgt/facility/$fac/api/generate") }}', function (result) {
          if (result) $('#apikey').attr('value', result)
        })
      }

      function apiSBGen () {
        $.post('{{ secure_url("/mgt/facility/$fac/api/generate/sandbox") }}', function (result) {
          if (result) $('#apisbkey').attr('value', result)
        })
      }

      @endif
      @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
      function ipUpdate () {
        $.post('{{ secure_url("/mgt/facility/$fac/api/update") }}', {apiip: $('#apiip').val()}).done(function (result) {
          if (result == 1) bootbox.alert('Updated')
        })
      }

      function ipSBUpdate () {
        $.post('{{ secure_url("/mgt/facility/{$fac}/api/update/sandbox") }}', {apiip: $('#apisbip').val()}).done(function (result) {
          if (result == 1) bootbox.alert('Updated')
        })
      }

      function appvTrans (id) {
        bootbox.confirm('Confirm approval?', function (result) {
          if (result) {
            $.post('{{ secure_url('/mgt/ajax/transfers/1') }}', {id: id}, function (data) {
              bootbox.alert(data)
              window.refresh()
              location.reload(true)
            })
          }
        })
      }

      function rejTrans (id) {
        bootbox.prompt('Reason for rejection:', function (result) {
          if (result === null) {
          } else {
            $.post('{{ secure_url('/mgt/ajax/transfers/2') }}', {id: id, reason: result}, function (data) {
              bootbox.alert(data)
              window.refresh()
              location.reload(true)
            })
          }
        })
      }

      function deleteController (cid) {
        bootbox.prompt('Reason for delete:', function (result) {
          if (result === null) {
            return
          } else {
            $.ajax({
              url : $.apiUrl() + '/v2/facility/{{$fac}}/roster/' + cid,
              type: 'DELETE',
              data: {'reason': result}
            }).success(function () {
              location.reload(true)
            })
          }
        })
      }

      function posDel (val) {
        var val_lng
        switch (val) {
          case 1:
            val_lng = 'ATM'
            break
          case 2:
            val_lng = 'DATM'
            break
          case 3:
            val_lng = 'TA'
            break
          case 4:
            val_lng = 'EC'
            break
          case 5:
            val_lng = 'FE'
            break
          case 6:
            val_lng = 'WM'
            break
        }
        bootbox.confirm('Confirm vacancy of ' + val_lng + ' ?', function (result) {
          if (result) {
            $.post("{{secure_url('mgt/ajax/del/position/'.$fac)}}", {pos: val}, function (data) {
              bootbox.alert(data)
              $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                $('#staff-table').html(data)
              })
            })
          }
        })
      }

      function posEdit (val) {
        switch (val) {
          case 1:
            bootbox.prompt('Enter new CID for ATM', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/1')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })
            break
          case 2:
            bootbox.prompt('Enter new CID for DATM', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/2')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })
            break
          case 3:
            bootbox.prompt('Enter new CID for TA', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/3')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })
            break
          case 4:
            bootbox.prompt('Enter new CID for EC', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/4')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })
            break
          case 5:
            bootbox.prompt('Enter new CID for FE', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/5')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })
            break
          case 6:
            bootbox.prompt('Enter new CID for WM', function (result) {
              if (result === null) {
              } else {
                $.post("{{secure_url('mgt/ajax/position/'.$fac.'/6')}}", {cid: result}, function (data) {
                  bootbox.alert(data)
                  $.post('{{secure_url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                    $('#staff-table').html(data)
                  })
                })
              }
            })

            break
        }
      }
        @endif
    </script>
    <script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
@stop
