<!DOCTYPE html>
<html>
<head>
    <title>VATUSA Public API Documentation</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        ul.apinav {
            margin-top: 15px
        }

        footer {
            text-align: center;
            font-size: 10px;
            color: gray
        }

        .italic {
            font-style: italic
        }
        .nav-pills.alt > .active > a, .nav-pills.alt > .active > a:hover, .nav-pills.alt > .active > a:focus {
            color: white;
            background-color: gray
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <ul class="apinav nav nav-pills">
                <li class="logo"><img alt="VATUSA" src="https://www.vatusa.net/img/logo.png" style="width: 200px;"></li>
                <li><a href="/">API Doc Home</a></li>
            </ul>
            <h1>VATUSA Public API Documentation</h1>
            <h5>by Daniel A. Hawton, VATUSA Data Services Manager</h5>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">Features</div>
            <div class="panel-body">
                <p>This documentation lists the public functions of the VATUSA API.</p>
                <ul>
                    <li>The data return types are <b>XML</b> and <b>JSON</b>.  These are set by adding an extension to the end of the request, IE, <i>.xml</i>, <i>.json</i>. <b>Default</b> JSON</li>
                    <li>The max return for each request is 100 items.  This is set by appending a comma and the number requested after the extension.  IE, <i>,5</i> <b>Default</b> 100</li>
                    <li>News and event items are pulled from the forums and contain BB Code.  More information can be found <a href="http://wiki.simplemachines.org/smf/Bulletin_board_code">here</a>.</li>
                <p></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Events</h3></div>
            <div class="panel-body">
                To get upcoming and current events beginning today, up to and including <i>limit</i>.
<pre><code>https://api.vatusa.net/events         # Generic request
https://api.vatusa.net/events.xml     # Get events in XML format
https://api.vatusa.net/events.json,5  # Get the next 5 events in JSON format
</code></pre>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">News</h3></div>
            <div class="panel-body">
                To get current news items, up to and including <i>limit</i>.  These are items listed on the homepage or retrieved from <a href="https://www.vatusa.net/forums/index.php?board=47.0">https://www.vatusa.net/forums/index.php?board=47.0</a>.
                <pre><code>https://api.vatusa.net/news         # Generic request
https://api.vatusa.net/news.xml     # Get news items in XML format
https://api.vatusa.net/news.json,5  # Get 5 news items in JSON format
</code></pre>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Roster</h3></div>
            <div class="panel-body">
                To get basic, public roster information for a facility.  Limit is not used.
<pre><code>https://api.vatusa.net/roster-(FACILITY ID)          # Generic request format
https://api.vatusa.net/roster-ZAN         # Get ZAN Roster in standard JSON format
https://api.vatusa.net/roster-ZAN.xml     # Get ZAN Roster in XML format</code></pre>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Planned features</h3></div>
            <div class="panel-body">
                Some of the features planned are (US-only):
                <ul>
                    <li>Live NOTAMs including NATs</li>
                    <li>Live TFRs</li>
                    <li>Live SIGMET and AIRMET information incl. Convective SIGMET</li>
                    <li>Live METAR/SPECI data</li>
                    <li>Airport Facility Information (name, location, lat/lng, runway information, etc)</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">VATUSA Facility-only Features</h3></div>
            <div class="panel-body">
                There are a number of features that have interaction with users and data and are restricted.  Senior Staff and webmasters
                can view that documentation by heading over to <a href="https://www.vatusa.net/info/policies">https://www.vatusa.net/info/policies</a>
                and reading VATUSA Technical 1000.2 (T1000.2) - API - Next Gen.  If the view link is not available, you must be logged in and have the appropriate
                permissions (ATM, DATM, VATUSA Staff or Webmaster).
            </div>
        </div>
    </div>
</div>
</body>
</html>