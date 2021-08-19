@extends('layout')
@section('title', 'How to join VATUSA')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>Join as ATC</h3>
                <p>Thank you for taking the time to consider joining the Virtual Air Traffic
                    Simulation Network's
                    USA Division (VATUSA). This network represents the single, most complex airspace in the world
                    featuring 2015's world's
                    most busiest airport, and 4 of the top 10 (according to Airports Council International). Each day,
                    on average, over 2,000,000
                    passengers traverse across the US airspace on Air Transport (followed by China at just over 1
                    million, and the UK at 350,000). VATUSA airspace comprises of 20 Air Route Traffic
                    Control Centers (contiguous US and Anchorage), 1 Control Facility (Pacific Control Facility) and 1
                    CERAP (Guam CERAP, under management of PCF).</p>
                <div class="alert alert-warning"><i class="fa fa-info-circle"></i> <b>INFO:</b> Controller information
                    is updated on login. You may have to log out and log back in to see updated information from VATSIM.
                </div>
                <div>
                    {!! $content !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Join as Pilot</h3>
                <p>To join a pilot, all you need is a <a href="https://www.vatsim.net">VATSIM account</a>. For more
                    information,
                    see VATSIM's <a href="https://www.vatsim.net/pilots/getting-started">Getting Started</a> page.</p>
            </div>
        </div>
    </div>
@endsection
