@extends('layout')
@section('title', 'TMU Notices (N.T.O.S.)')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">TMU Notices (N.T.O.S.)</h3><h5 class="help-block">This page refreshes every 30
                    seconds.</h5>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-left:-5px;">
                    <div class="form-group col-xs-4">
                        <label for="sectorselect">
                            Select Facility/Sector
                        </label>
                        <select class="form-control" id="sectorselect" name="tmu_facility_id" autocomplete="off"
                                onchange="window.location = '/tmu/notices/'+this.value">
                            <option @if(!$sector) selected @endif value="">-- All Sectors --</option>
                            @foreach($facilitiesArr as $fac => $sect)
                                <optgroup label="{{ \App\Classes\Helper::facShtLng($fac) }}">
                                    @foreach($sect as $i => $s)
                                        <option
                                            value="{{ $s['id'] }}"
                                            @if($sector == $s['id']) selected @endif> {{ $s['name'] }} @if($s['name'] == \App\Classes\Helper::facShtLng($fac))
                                                (All Sectors) @endif</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
                <section>
                    @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isVATUSAStaff())
                        <div class="text-center">
                            <a href="{{ secure_url("/mgt/tmu#notices") }}">
                                <button class="btn btn-default"><i class="fa fa-pencil-alt"></i>
                                    Edit @if(!\App\Classes\RoleHelper::isVATUSAStaff()) {{ \Illuminate\Support\Facades\Auth::user()->facility }} @endif
                                    Notices
                                </button>
                            </a>
                        </div>
                    @endif
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width:10%;">Facility/Sector</th>
                            <th style="width:15%;">Date</th>
                            <th style="width:60%;">Notice</th>
                            <th style="width:15%;">Expiration Date (UTC)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!$notices->count())
                            <tr class="warning">
                                <td colspan="4" style="text-align: center">
                                    <i class="fa fa-info-circle"></i> No Notices Active
                                </td>
                            </tr>
                        @else
                            @foreach($notices as $notice)
                                @php
                                    $priority = $notice->priority;
                                    switch($priority) {
                                        case 1: $rcolor =  'warning'; break;
                                        case 2: $rcolor =  'success'; break;
                                        case 3: $rcolor = 'danger'; break;
                                        default: $rcolor = ''; break;
                                     }
                                @endphp
                                <tr class="{{ $rcolor }}">
                                    <td>{{ $notice->tmuFacility->name }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($notice->start_date)->format('m/d/Y H:i') }}</td>
                                    <td>{!! $notice->message !!}</td>
                                    <td>{!! $notice->expire_date ? $notice->expire_date->format('m/d/Y H:i') : "<em>Indefinite</em>" !!}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    {{ $notices->links() }}
                </section>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
          setTimeout(() => location.reload(true), 30000)
        </script>
    @endpush
@endsection