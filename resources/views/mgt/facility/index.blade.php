@extends('layout')
@section('title', 'Facility Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor())
                        <select id="facmgt" class="mgt-sel">
                            @foreach(\App\Models\Facility::where('active', 1)->orderby('id', 'ASC')->get() as $f)
                                <option name="{{$f->id}}" @if($f->id == $fac) selected @endif>{{$f->id}}</option>
                            @endforeach
                        </select>
                        - Facility Management
                    @else
                        Facility Management - {{ Auth::user()->facility()->name }}
                    @endif
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#dash" aria-controls="dash" role="tab" data-toggle="tab">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation"><a href="#trans" aria-controls="trans" role="tab" data-toggle="tab"><i
                                            class="fas fa-exchange-alt"></i> Transfers</a>
                            </li>
                        @endif
                        <li role="presentation">
                            <a href="#hroster" aria-controls="hroster" role="tab" data-toggle="tab">
                                <i class="fas fa-users"></i> Home Roster
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#vroster" aria-controls="vroster" role="tab" data-toggle="tab">
                                <i class="fas fa-door-open"></i> Visiting Roster
                            </a>
                        </li>
                        @if(\App\Classes\RoleHelper::isTrainingStaff(\Auth::user()->cid, false))
                            <li role="presentation">
                                <a href="{{ url("mgt/facility/training/stats") }}" aria-controls="training">
                                    <i class="fas fa-chart-line"></i> Training
                                </a>
                            </li>
                        @endif
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation">
                                <a href="#uls" aria-controls="uls" role="tab" data-toggle="tab">
                                    <i class="fas fa-server"></i> Tech Conf
                                </a>
                            </li>
                        @endif
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <li role="presentation">
                                <a href="#email" aria-controls="email" role="tab" data-toggle="tab">
                                    <i class="fas fa-envelope"></i> Email Conf
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="dash">
                            @include("mgt.facility.parts.dashboard")
                        </div>
                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="trans">
                                @include("mgt.facility.parts.transfers")
                            </div>
                        @endif
                        <div role="tabpanel" class="tab-pane" id="hroster">
                            @include("mgt.facility.parts.home_roster")
                        </div>
                        <div role="tabpanel" class="tab-pane" id="vroster">
                            @include("mgt.facility.parts.visiting_roster")
                        </div>
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="uls">
                                @include("mgt.facility.parts.tech")
                            </div>
                        @endif
                        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))
                            <div role="tabpanel" class="tab-pane" id="email">
                                @include("mgt.facility.parts.email")
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Visitor Modal -->
    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM"))
        <div class="modal fade" id="addVisitorModal" tabindex="-1" role="dialog" aria-labelledby="addVisitorModalTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="addVisitorModalTitle">Add Visitor</h5>
                    </div>

                    <div class="modal-body">

                        <label for="cid">CID or Last Name:</label>
                        <input type="text" name="cid" class="form-control" id="cidsearch">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="addButton" class="btn btn-sm btn-success">Add</button>
                    </div>

                </div>
            </div>
        </div>
    @endif
    <!-- Staff Assignment Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" role="dialog" aria-labelledby="assignStaffModalTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="assignStaffModalTitle">Assigning new <span id="staffPosition"></span>
                        for {{ $fac }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <label for="cid">CID or Last Name:</label>
                    <input type="text" name="cid" class="form-control" id="staffcidsearch">
                    <input type="number" name="pos" id="staffInt" hidden>
                    Transfer to facility? <span id="toggleTransfer" style="font-size:1.8em;margin-left: 20px;">
                                <i class="toggle-icon fa fa-toggle-on text-success"></i>
                                <i class="spinner-icon fa fa-spinner fa-spin" style="display:none;"></i>
                    </span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="confirmAssignStaff" class="btn btn-sm btn-success">Add</button>
                </div>

            </div>
        </div>
    </div>
    <script>
        $('#toggleTransfer').click(function () {
            let icon = $(this).find('i.toggle-icon'),
                currentlyOn = icon.hasClass('fa-toggle-on')

            icon.attr('class', 'toggle-icon fa fa-toggle-' + (currentlyOn ? 'off' : 'on') +
                ' text-' + (currentlyOn ? 'danger' : 'success'))
        })
    </script>


    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#facmgt').change(function () {
                window.location = '/mgt/facility/' + $('#facmgt').val()
            })
            var hash = document.location.hash
            if (hash)
                $('.nav-tabs a[href=' + hash + ']').tab('show')

            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                history.pushState({}, '', e.target.hash)
            })

            $.ajax({
                url: $.apiUrl() + '/v2/facility/{{$fac}}/roster/home',
                type: 'GET'
            }).success(function (resp) {
                var html = ''
                $.each(resp.data, function (i) {
                    if (resp.data[i].cid == undefined) return
                    html += '<tr><td>' + resp.data[i].cid + '</td>'
                    html += '<td data-sort-value="' + resp.data[i].lname + ', ' + resp.data[i].fname + '">'
                    if (resp.data[i].isSupIns == true || (resp.data[i].rating > 7 && resp.data[i].rating < 11)) html += ' <span class=\'label label-danger role-label\'>INS</span> '
                    else if (resp.data[i].isMentor == true) html += '<span class=\'label label-danger role-label\'>MTR</span> '
                    html += resp.data[i].lname + ', ' + resp.data[i].fname
                    html += '</td>'
                    html += '<td data-text="' + resp.data[i].rating + '"><span style="display:none">' + String.fromCharCode(64 + parseInt(resp.data[i].rating)) + '</span>' + resp.data[i].rating_short
                    html += '</td>'
                    var date = new Date(resp.data[i].facility_join.replace(/\s/, 'T'))
                    html += '<td>' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + '</td>'
                    var last_promotion = resp.data[i].last_promotion
                    if (last_promotion) var promotion_date = new Date(last_promotion.replace(/\s/, 'T'))
                    if (promotion_date) html += '<td>' + (promotion_date.getMonth() + 1) + '/' + promotion_date.getDate() + '/' + promotion_date.getFullYear() + '</td>'
                    else html += '<td><span class="text-muted">N/A</span></td>'
                    html += '<td class="text-right">'
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaff(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isInstructor(\Auth::user()->cid, $fac))
                    if (resp.data[i].promotion_eligible == true) {
                        html += '<a href="/mgt/controller/' + resp.data[i].cid + '/promote"><i class="text-yellow fa fa-star"></i></a> &nbsp; '
                    }
                    @endif
                        html += '<a href="/mgt/controller/' + resp.data[i].cid + '"><i class="fa fa-search"></i></a>'
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff())
                        html += ' &nbsp; <a href="#" onClick="deleteController(' + resp.data[i].cid + ', \'' + resp.data[i].fname + ' ' + resp.data[i].lname + '\')"><i class="text-danger fa fa-times"></i></a>'
                    @endif
                        html += '</td></tr>'
                })
                $('#hrostertablebody').html(html)
                $('#hrostertable').toggle()
                $('#hrosterloading').toggle()
                $('#hrostertable').tablesorter({
                    textExtraction: function (node) {
                        return $(node).attr('data-sort-value') || $(node).text();
                    }
                })
            })

            $.ajax({
                url: $.apiUrl() + '/v2/facility/{{$fac}}/roster/visit',
                type: 'GET'
            }).success(function (resp) {
                var html = ''
                $.each(resp.data, function (i) {
                    if (resp.data[i].cid == undefined) return
                    html += '<tr><td>' + resp.data[i].cid + '</td>'
                    html += '<td>'
                    if (resp.data[i].isSupIns == true) html += ' <span class=\'label label-danger role-label\'>INS</span> '
                    else if (resp.data[i].isMentor == true) html += '<span class=\'label label-danger role-label\'>MTR</span> '
                    html += resp.data[i].lname + ', ' + resp.data[i].fname
                    html += '</td>'
                    html += '<td data-text="' + resp.data[i].rating + '"><span style="display:none">' + String.fromCharCode(64 + parseInt(resp.data[i].rating)) + '</span>' + resp.data[i].rating_short
                    html += '</td>'
                    html += '<td>' + resp.data[i].facility + '</td>'
                    var date = new Date(resp.data[i].facility_join.replace(/\s/, 'T'))
                    html += '<td>' + (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear() + '</td>'
                    html += '<td class="text-right">'
                    html += '<a href="/mgt/controller/' + resp.data[i].cid + '"><i class="fa fa-search"></i></a>'
                    @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::isVATUSAStaff())
                        html += ' &nbsp; <a href="#" onClick="deleteVisitor(' + resp.data[i].cid + ', \'' + resp.data[i].fname.replace(/'/g, "\\'") + ' ' + resp.data[i].lname.replace(/'/g, "\\'") + '\')"><i class="text-danger fa fa-times"></i></a>'
                    @endif
                        html += '</td></tr>'
                })
                $('#vrostertablebody').html(html)
                $('#vrostertable').toggle()
                $('#vrosterloading').toggle()
                $('#vrostertable').tablesorter()
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
                url: `${$.apiUrl()}/v2/email/hosted?facility={{$fac}}`,
                method: 'GET',
                dataType: 'JSON'
            }).done((resp) => {
                let html
                $.each(resp.data.emails, i => {
                    html = `${html}
                <tr><td style="vertical-align: middle">${resp.data.emails[i].username}</td><td style="vertical-align: middle">${resp.data.emails[i].cid}</td><td><button class="btn btn-danger nhDelete" data-username="${resp.data.emails[i].username}">Delete</button></td></tr>
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
                url: `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${$(e.currentTarget).data('username')}`
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
                url: `${$.apiUrl()}/v2/email/hosted/{{$fac}}/${email}`,
                data: {cid}
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
                method: 'GET',
                url: `${$.apiUrl()}/v2/email/{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`,
                dataType: 'json',
                xhrFields: {
                    includeCredentials: true
                }
            }).done(resp => {
                waitingDialog.hide()
                if (resp.data.type === 'STATIC') {
                    $('#emailStatic').val('1')
                } else {
                    $('#emailStatic').val('0')
                }
                $('#emailDestination').val(resp.data.destination)
                $('#emailBox').show()
            }).fail(data => {
                waitingDialog.hide()
                bootbox.alert(`Problem handling this request ${data.msg}`)
            })
        }).on('click', '.btnEmailSave', function () {
            waitingDialog.show()
            $.ajax({
                method: 'POST',
                url: `${$.apiUrl()}/v2/email`,
                data: {
                    email: `{{$fac}}-${$('#facilityEmail').val()}@vatusa.net`,
                    destination: $('#emailDestination').val(),
                    static: ($('#emailStatic').val() == '1') ? true : false
                }
            }).done(data => {
                waitingDialog.hide()
                bootbox.alert('Changes have been saved.')
            }).fail(data => {
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
                url: `${$.apiUrl()}/v2/facility/{{$fac}}/email/${$('#facilityEmailTemplate').val()}`,
                method: 'GET'
            }).done(resp => {
                $('#emailTemplateBody').val(resp.data.body)
                waitingDialog.hide()
                $('#emailTemplateBox').show()
                $('#emailTemplateVariableList').html('')
                for (let variable of resp.data.variables) {
                    $('#emailTemplateVariableList').append(`<li>$${variable}</li>`)
                }
            }).fail(data => {
                waitingDialog.hide()
                bootbox.alert('Failed to load email template from API, got: ' + data)
            })
        }).on('click', '.btnEmailTemplateSave', () => {
            waitingDialog.show()
            $.ajax({
                url: `${$.apiUrl()}/v2/facility/{{$fac}}/email/${$('#facilityEmailTemplate').val()}`,
                method: 'POST',
                data: {body: $('#emailTemplateBody').val()}
            }).done(() => {
                bootbox.alert('Template saved successfully.')
                waitingDialog.hide()
            }).fail(data => {
                bootbox.alert(`Template save failed`)
                waitingDialog.hide()
            })
        })

        @endif
        @if(\App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM") || \App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))

        function updateUrl() {
            $.ajax(
                {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {url: $('#facurl').val()}}
            ).done(function (result) {
                bootbox.alert('URL saved successfully')
            }).fail(function (result) {
                bootbox.alert('URL save failed. ' + result.responseJSON.msg + '.')
            })
        }

        function updateDevUrl() {
            $.ajax(
                {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {url_dev: $('#facurldev').val()}}
            ).done(function (result) {
                bootbox.alert('Dev URL saved successfully.')
            }).fail(function (result) {
                bootbox.alert('Dev URL save failed. ' + result.responseJSON.msg + '.')
            })
        }

        function apiv2JWK(isdev = false) {
            $.ajax(
                {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {apiV2jwk: '', jwkdev: isdev}}
            ).done(function (result) {
                if (result.hasOwnProperty('status') && result.status === 'OK') {
                    if (!isdev) $('#textapiv2jwk').val(result.api_jwk)
                    else $('#textapiv2jwkdev').val(result.api_jwk_dev)
                }
            })
        }

        function clearDevAPIv2JWK() {
            $.ajax(
                {method: 'put', url: $.apiUrl() + "/v2/facility/{{$fac}}", data: {apiV2jwk: 'X', jwkdev: true}}
            ).done(function (result) {
                if (result.hasOwnProperty('status') && result.status === 'OK') {
                    $('#textapiv2jwkdev').val('')
                }
            })
        }

        function apiGen() {
            $.post('{{ url("/mgt/facility/$fac/api/generate") }}', function (result) {
                if (result) $('#apikey').attr('value', result)
            })
        }

        function apiSBGen() {
            $.post('{{ url("/mgt/facility/$fac/api/generate/sandbox") }}', function (result) {
                if (result) $('#apisbkey').attr('value', result)
            })
        }

        @endif
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac))

        function appvTrans(id) {
            swal({
                title: 'Approving Transfer',
                text: 'Are you sure you want to approve this transfer? This can only be undone by VATUSA Staff.',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then(r => {
                    if (r) {
                        $.post('{{ url('/mgt/ajax/transfers/1') }}', {id: id}, function (data) {
                            if (data == 1)
                                swal('Success!', 'The transfer has been successfully approved.', 'success').then(_ => {
                                    window.location.hash = '#trans';
                                    location.reload()
                                })
                            else
                                swal('Error!', 'The transfer could not be approved. It may have already been processed.', 'error')
                        }).error(_ => {
                            swal('Error!', 'The transfer could not be approved. A server error has occurred.', 'error')
                        })
                    } else {
                        return null
                    }
                })
        }

        function rejTrans(id) {
            swal({
                title: 'Rejecting Transfer',
                text: 'Are you sure you want to reject this transfer? This can only be undone by VATUSA Staff.',
                icon: 'warning',
                content: {
                    element: 'input',
                    attributes: {
                        placeholder: 'Reason for rejection...'
                    }
                },
                buttons: true,
                dangerMode: true,
            })
                .then((r) => {
                    if (r) {
                        $.post('{{ url('/mgt/ajax/transfers/2') }}', {id: id, reason: r}, function (data) {
                            if (data == 1)
                                swal('Success!', 'The transfer has been successfully rejected.', 'success').then(_ => {
                                    window.location.hash = '#trans';
                                    location.reload()
                                })
                            else
                                swal('Error!', 'The transfer could not be rejected. It may have already been processed.', 'error')
                        }).error(_ => {
                            swal('Error!', 'The transfer could not be rejected. A server error has occurred.', 'error')
                        })
                    }
                })
        }

        function deleteController(cid, name) {
            swal({
                title: 'Deleting Controller - ' + name,
                text: 'Are you sure you want to delete this home controller? This can only be undone by VATUSA Staff.',
                icon: 'warning',
                content: {
                    element: 'input',
                    attributes: {
                        placeholder: 'Reason for deletion...'
                    }
                },
                buttons: true,
                dangerMode: true,
            })
                .then((r) => {
                    if (r) {
                        $.ajax({
                            url: $.apiUrl() + '/v2/facility/{{$fac}}/roster/' + cid,
                            type: 'DELETE',
                            data: {'reason': r}
                        }).success(function () {
                            swal('Success!', 'The controller has been deleted.', 'success').then(_ => {
                                window.location.hash = '#hroster'
                                location.reload()
                            })
                        }).error(_ => {
                            swal('Error!', 'Unable to delete controller. A server error has occurred.', 'error')
                        })
                    }
                })
        }

        function posDel(val) {
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
            swal({
                title: 'Vacating ' + val_lng + ' Position',
                text: 'Are you sure you want to vacate this position?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then(r => {
                    if (r) {
                        $.post("{{url('mgt/ajax/del/position/'.$fac)}}", {pos: val}, function (data) {
                                if (data == 1) {
                                    swal('Success!', 'The ' + val_lng + ' position has been successfully vacated.', 'success')
                                    $.post('{{url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                                        $('#staff-table').html(data)
                                    })
                                } else {
                                    swal('Error!', 'Unable to vacate the posititon.', 'error')
                                }
                            }
                        ).error(_ => swal('Error!', 'Unable to vacate the posititon. A server error has occurred.', 'error'))
                    } else {
                        return null
                    }
                })
        }

        function posEdit(val) {
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
            prevVal = ''
            $('#assignStaffModal #staffcidsearch').devbridgeAutocomplete().setOptions({lookup: []})
            $('#assignStaffModal #staffcidsearch').val('')
            $('#assignStaffModal #staffPosition').text(val_lng)
            $('#assignStaffModal #staffInt').val(val)
            $('#assignStaffModal').modal('show')
            $('#confirmAssignStaff').unbind()
            $('#confirmAssignStaff').click(function () {
                $.post("{{url('mgt/ajax/position/'.$fac)}}/" + val, {
                    cid: $('#assignStaffModal #staffcidsearch').val(),
                    xfer: $('#toggleTransfer').find('i.toggle-icon').hasClass('fa-toggle-on')
                }, function (data) {
                    bootbox.alert(data)
                    $.post('{{url('/mgt/ajax/staff/'.$fac)}}', function (data) {
                        $('#staff-table').html(data)
                    })
                })
                $('#assignStaffModal').modal('hide')
            })
        }

        @endif
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaffExceptTA(\Auth::user()->cid, $fac) || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, $fac, "WM"))

        function deleteVisitor(cid, name) {
            swal({
                title: 'Deleting Visitor - ' + name,
                text: 'Are you sure you want to delete this visiting controller?',
                icon: 'warning',
                content: {
                    element: 'input',
                    attributes: {
                        placeholder: 'Reason for deletion...'
                    }
                },
                buttons: true,
                dangerMode: true,
            })
                .then((r) => {
                    if (r) {
                        $.ajax({
                            url: $.apiUrl() + '/v2/facility/{{$fac}}/roster/manageVisitor/' + cid,
                            type: 'DELETE',
                            data: {'reason': r}
                        }).success(function () {
                            swal('Success!', 'The controller has been deleted.', 'success').then(_ => {
                                window.location.hash = '#vroster'
                                location.reload()
                            })
                        }).error(_ => {
                            swal('Error!', 'Unable to delete controller. A server error has occurred.', 'error')
                        })
                    }
                })
        }

        $('#addButton').click(function () {
            // Setting Values
            var cid = $('#cidsearch').val()

            $.ajax({
                url: $.apiUrl() + '/v2/facility/{{$fac}}/roster/manageVisitor/' + cid,
                type: 'POST',
            }).success(function (res) {
                window.location.hash = '#vroster';
                location.reload()
            }).error(function () {
                alert('Error occurred')
            })
        })

        $('#cidsearch').devbridgeAutocomplete({
            lookup: [],
            onSelect: (suggestion) => {
                $('#cidsearch').val(suggestion.data)
            }
        })
        $('#staffcidsearch').devbridgeAutocomplete({
            lookup: [],
            onSelect: (suggestion) => {
                $('#staffcidsearch').val(suggestion.data)
            }
        })

        var prevVal = ''

        $('#cidsearch, #staffcidsearch').on('change keydown keyup paste', function () {
            let newVal = $(this).val()
            if (newVal.length === 4 && newVal !== prevVal) {
                let url = '/v2/user/' + (isNaN(newVal) ? 'filterlname/' : 'filtercid/')
                prevVal = newVal
                $.get($.apiUrl() + url + newVal)
                    .success((data) => {
                        $(this).devbridgeAutocomplete().setOptions({
                            lookup: $.map(data.data, (item) => {
                                return {value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid}
                            })
                        })
                        $(this).focus()
                    })
            }
        })
        @endif
    </script>
    <script type="text/javascript" src="/js/jquery.tablesorter.min.js"></script>
@stop
