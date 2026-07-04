@extends('layout')
@section('title', 'Welcome')
@section('content')
    <div class="container" id="home-container">
        <br>
        <div class="row">
            <div class="col-md-12 link-underline" style="margin-bottom: 16px; padding: 24px 28px; font-size: 1.15em; border: 3px solid #B22234; border-radius: 6px; background: linear-gradient(135deg, #f8f9ff 0%, #eef2ff 100%); box-shadow: 0 2px 8px rgba(0,0,0,0.10);">
                <div style="margin-bottom: 16px; padding: 14px 18px; background: #B22234; color: #fff; border-radius: 4px; font-size: 1.12em; font-weight: 600;">
                    <i class="fa fa-info-circle"></i>
                    You are viewing the <strong>legacy VATUSA site</strong>. The site is being migrated to a new platform &mdash; visit
                    <a href="https://www.vatusa.net/" style="color: #fff; text-decoration: underline; font-weight: 700;">www.vatusa.net</a>
                    for the latest content.
                </div>
                <p style="margin-bottom: 0;">
                    VATUSA is a division of the <a href="http://www.vatsim.net/">VATSIM</a> Americas region comprising
                    of almost all airspace operated by the real
                    world Federal Aviation Administration. The airspace comprises of 21 Air Route Traffic Control
                    Centers, 1 Combined Control Facility (Honolulu Control Facility) and 1 CERAP (Guam
                    CERAP, under management of HCF). All
                    information contained within this website is designated for use with the VATSIM network and for
                    flight
                    simulation purposes. Information is not intended nor should be used for
                    real world navigation and its use for real world navigation could be in violation of federal laws.
                    This
                    website is not affiliated with the Federal Aviation Administration or any other governing body.
                </p>
            </div>
        </div>
        <br>
        @if($notices->count())
            <div class="row">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">TMU Notices (N.T.O.S.) <a href="{{ url("/tmu/notices") }}"
                                                                          target="_blank"><i
                                        class="fa fa-share-square"></i></a></h3>
                    </div>
                    <div class="panel-body">
                        <section>
                            @if(\App\Helpers\AuthHelper::authACL()->isFacilityStaff() ||
                                \App\Helpers\AuthHelper::authACL()->isInstructor() ||
                                \App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                                <div class="text-center">
                                    <a href="{{ url("/mgt/tmu#notices") }}">
                                        <button class="btn btn-default"><i class="fa fa-pencil-alt"></i>
                                            Edit @if(!\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                                                {{ \Illuminate\Support\Facades\Auth::user()->facility }}
                                            @endif
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
                            <div class="text-center">
                                {{ $notices->links() }}
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop
