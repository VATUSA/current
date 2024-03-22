@extends('layout')
@section('title', 'Welcome')
@section('content')
    @if(!empty($banners) && !empty($ids))
        <div class="c-wrapper">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    @for($i = 0 ; $i < count($banners) ; $i++)
                        <div class="item{{($i==0)? " active":""}}">
                            <a href="https://forums.vatusa.net/index.php?topic={{$ids[$i]}}.0"><img
                                    src="{{$banners[$i]}}"
                                    alt="Event Banner"></a>
                            <div class="carousel-caption"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    @endif
    <div class="container" id="home-container">
        <br>
        <div class="row">
            <div class="col-md-12 alert alert-info link-underline" style="margin-bottom: 0">
                <p>
                    VATUSA is a division of the <a href="http://www.vatsim.net/">VATSIM</a> Americas region comprising
                    of almost all airspace operated by the real
                    world Federal Aviation Administration. The airspace comprises of 20 Air Route Traffic Control
                    Centers
                    (contiguous US and Anchorage), 1 Control Facility (Honolulu Control Facility) and 1 CERAP (Guam
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
                            @if(\App\Classes\AuthHelper::isFacilityStaff() ||
                                \App\Classes\AuthHelper::isInstructor() ||
                                \App\Classes\AuthHelper::isVATUSAStaff())
                                <div class="text-center">
                                    <a href="{{ url("/mgt/tmu#notices") }}">
                                        <button class="btn btn-default"><i class="fa fa-pencil-alt"></i>
                                            Edit @if(!\App\Classes\AuthHelper::isVATUSAStaff()) {{ \Illuminate\Support\Facades\Auth::user()->facility }} @endif
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
        <div class="row" id="bulletins">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Recent News
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table id="newsbody">
                            <tr>
                                <td><i class="fa fa-cog fa-spin"></i> Loading...</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Upcoming Events
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table id="eventbody">
                            <tr>
                                <td><i class="fa fa-cog fa-spin"></i> Loading...</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ secure_asset("js/moment.js") }}" type="text/javascript"></script>
        <script type="text/javascript">
          $(document).ready(function () {
            $.ajax({
              url : 'https://api.vatusa.net/v2/public/news/10',
                beforeSend: function(xhr) { xhr.withCredentials = true },
              type: 'GET'
            }).success(function (resp) {
              var html = ''
              $.each(resp.data, function (i) {
                if(resp.data[i].subject === undefined) return
                html = html + '<tr onClick="window.location=\'https://forums.vatusa.net/index.php?topic=' + resp.data[i].id_topic + '\';" style="cursor: pointer">'
                html = html + '<td style="padding-right: 8px; padding-top: 6px" valign="top"><i class="fa fa-file-alt fa-2x"></i></td>'
                html = html + '<td><p><strong>' + resp.data[i].subject + '</strong><br><small>' + moment(moment.unix(resp.data[i].poster_time)).format('MM/DD/YYYY') + '</small></p></td></tr>'
              })
              $('#newsbody').html(html)
            })
            $.ajax({
              url : 'https://api.vatusa.net/v2/public/events/10',
                beforeSend: function(xhr) { xhr.withCredentials = true },
              type: 'GET',
            }).success(function (resp) {
              var html = ''
              $.each(resp.data, function (i) {
                if(resp.data[i].title === undefined) return
                html = html + '<tr onClick="window.location=\'https://forums.vatusa.net/index.php?topic=' + resp.data[i].id_topic + '\';" style="cursor: pointer">'
                html = html + '<td style="padding-right: 8px; padding-top: 6px" valign="top"><i class="fa fa-plane fa-2x"></i></td>'
                html = html + '<td><p><strong>' + resp.data[i].title + '</strong><br><small>' + moment(resp.data[i].start_date).format('MM/DD/YYYY') + '</small></p></td></tr>'
              })
              $('#eventbody').html(html)
            })
          })
        </script>
    @endpush
@stop
