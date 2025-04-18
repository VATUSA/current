@extends('layout')
@section('title', 'Facility Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(\App\Helpers\AuthHelper::authACL()->canViewAnyFacilityRoster())
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
                        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
                            <li role="presentation">
                                <a href="#trans" aria-controls="trans" role="tab" data-toggle="tab">
                                    <i class="fas fa-exchange-alt"></i> Pending Transfers
                                </a>
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
                        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTechConfig($fac))
                            <li role="presentation">
                                <a href="#uls" aria-controls="uls" role="tab" data-toggle="tab">
                                    <i class="fas fa-server"></i> Tech Conf
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="dash">
                            @include("mgt.facility.parts.dashboard")
                        </div>
                        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
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
                        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTechConfig($fac))
                            <div role="tabpanel" class="tab-pane" id="uls">
                                @include("mgt.facility.parts.tech")
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Visitor Modal -->
    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
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

            @if(\App\Helpers\AuthHelper::authACL()->canPromoteForFacility($fac))
            var advance = 1
            @elseif(\App\Helpers\AuthHelper::authACL()->canPromoteForFacility($fac, 2))
            var advance = 2
            @else
            var advance = 0
            @endif

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
                    if (resp.data[i].promotion_eligible == true && ((resp.data[i].rating == 1 && advance == 2) || advance == 1)) {
                        html += '<a href="/mgt/controller/' + resp.data[i].cid + '/promote"><i class="text-yellow fa fa-star"></i></a> &nbsp; '
                    }
                        html += '<a href="/mgt/controller/' + resp.data[i].cid + '"><i class="fa fa-search"></i></a>'
                    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
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
                    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))
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
        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTechConfig($fac))

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
        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))

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

        @endif
        @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityRoster($fac))

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
