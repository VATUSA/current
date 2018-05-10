@extends('layout')
@section('title', 'Welcome')
@section('content')
    <div class="c-wrapper">
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        @for($i = 0 ; $i < count($banners) ; $i++)
        <div class="item{{($i==0)? " active":""}}">
            <a href="https://forums.vatusa.net/index.php?topic={{$ids[$i]}}.0"><img src="{{$banners[$i]}}" alt="Event Banner"></a>
            <div class="carousel-caption"></div>
        </div>
        @endfor
    </div>
    </div>
    </div>
<div class="container">
    <br>
    <div col="row">
        <div class="col-md-12 alert alert-info link-underline">
                VATUSA is a division of the <a href="http://www.vatsim.net/">VATSIM</a> North American (<a href="http://vatna.org/">VATNA</a>) region comprising of almost all airspace operated by the real world Federal Aviation Administration.  The airspace comprises of 21 Air Route Traffic Control Centers (contiguous US and Anchorage),
                1 Control Facility (Honolulu Control Facility) and 1 CERAP (Guam CERAP, under management of HCF).  All information contained within this website is designated for use with the VATSIM network and for flight simulation purposes.  Information is not intended nor should be used for
                real world navigation and its use for real world navigation could be in violation of federal laws.  This website is not affiliated with the Federal Aviation Administration or any other governing body.
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Recent News
                    </h3>
                </div>
                <div class="panel-body">
                    <table id="newsbody">
                        <tr><td><i class="fa fa-cog fa-spin"></i> Loading...</td></tr>
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
                        <tr><td><i class="fa fa-cog fa-spin"></i> Loading...</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: 'https://api.vatusa.net/news.json,10',
            type: "GET"
        }).success(function(data) {
            data = JSON.parse(data);
            var html = "";
            $.each(data.news, function(i) {
                html = html + "<tr onClick=\"window.location='" + data.news[i].url + "';\" style=\"cursor: pointer\">";
                html = html + '<td style="padding-right: 8px; padding-top: 6px" valign="top"><i class="fa fa-file-text-o fa-2x"></i></td>';
                html = html + '<td><p><strong>' + data.news[i].subject + '</strong><br><small>' + data.news[i].humandate + '</small></p></td></tr>';
            });
            $('#newsbody').html(html);
        });
        $.ajax({
            url: 'https://api.vatusa.net/events,10',
            type: 'GET',
        }).success(function(data) {
            data = JSON.parse(data);
            var html = "";
            $.each(data.events, function(i) {
                html = html + "<tr onClick=\"window.location='" + data.events[i].url + "';\" style=\"cursor: pointer\">";
                html = html + '<td style="padding-right: 8px; padding-top: 6px" valign="top"><i class="fa fa-plane fa-2x"></i></td>';
                html = html + '<td><p><strong>' + data.events[i].title + '</strong><br><small>' + data.events[i].humandate + '</small></p></td></tr>';
            });
            $('#eventbody').html(html);
        });
    });
</script>
@stop
