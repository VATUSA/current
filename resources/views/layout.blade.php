<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VATUSA - @yield('title')</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-formhelpers.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.9.0/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link href="{{ mix('css/vatusa.css') }}" rel="stylesheet">
    @stack('styles')
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="/js/jquery.autocomplete.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        //Custom jQuery Elements
        $.apiUrl = () => ("{{ \App\Classes\Helper::apiUrl() }}")
        $.fn.ignore = function (sel) {
            return this.clone().find(sel || '>*').remove().end()
        }
    </script>

    @stack('scripts')
</head>
<body>
@if(Request::is('/') || Request::is(''))
    <style>
        .navbar-default {
            margin-bottom: 0px;
        }
    </style>
@endif
@if(Request::is('my/profile'))
    <style>
        .form-group {
            margin-bottom: 0px;
        }
    </style>
@endif
<div class="container-head">
    <div class="head">
        <div class="layer">
            <div class="container">
                <a href="/">
                    <img src="/img/logo-alt.png" class="logo" alt="VATUSA Home"/>
                </a>
                @if(Auth::check())
                    <div class="pull-right hidden-xs">
                        <div class="wb-usr">
                            <span class="grab"><strong>{{Auth::user()->fname.' '.Auth::user()->lname}}</strong></span>
                            <br>
                            <small><i class="fa fa-user"></i> {{Auth::user()->cid}} &nbsp; &mdash; &nbsp; <i
                                        class="fa fa-trophy"></i> {{\App\Classes\Helper::ratingShortFromInt(\Auth::user()->rating)}}
                                ({{Auth::user()->urating->long}})<br>
                                <i class="fa fa-star"></i> {{\App\Classes\RoleHelper::getUserRole(Auth::user()->cid, Auth::user()->facility)}}
                                - {{\App\Classes\Helper::facShtLng(Auth::user()->facility)}}
                                {{(!empty(Auth::user()->facility) && (Auth::user()->facility != 'HCF' && Auth::user()->facility != 'ZHQ' && !preg_match('/^ZZ/', Auth::user()->facility)) ? '' : '')}}
                                {{(!empty(Auth::user()->facility) && Auth::user()->facility == 'HCF' ? 'Control Facility' : '')}}
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="clear-fix"></div>
    <nav class="navbar navbar-default" id="nav">
        <div class="container">
            <div class="navbar-header">

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span
                            class="icon-bar"></span> <span class="icon-bar"></span></button>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="https://academy.vatusa.net"><i class="fas fa-graduation-cap"></i> Academy</a></li>
                    <li><a href="{{ url("/help/kb") }}"><i class="fas fa-question-circle"></i> FAQ</a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Facilities
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            @foreach(\App\Models\Facility::where(['active' => 1])->orderby('name', 'ASC')->get() as $f)
                                <li><a href="{{$f->url}}" target="_blank">{{$f->name}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Division Info <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="https://discord.gg/a7Qcse7" target="_blank"><i class="fab fa-discord"></i>
                                    Official Discord</a></li>
                            <li><a href="https://forums.vatusa.net"><i class="fas fa-comment"></i> Forums</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('info/members') }}"><i class="fas fa-users"></i> Members and
                                    Staff</a>
                            </li>
                            <li><a href="{{ url('info/policies') }}"><i class="fas fa-clipboard"></i> Policies
                                    and
                                    Downloads</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="https://forums.vatusa.net/?action=calendar"><i class="fas fa-calendar"></i>
                                    Events Calendar</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('info/ace') }}"><i class="fas fa-star"></i> ACE Team</a></li>
                            <li><a href="{{ url('info/dice') }}"><i class="fas fa-star"></i> DICE Team</a></li>
                            <li><a href="{{ url('info/solo') }}"><i class="fas fa-certificate"></i> Solo
                                    Certs</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Pilot Tools
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown-header">
                                <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">VATSIM Resources</h5>
                            </li>
                            <li><a href="https://vatsim.net/docs/basics/getting-started" target="_blank"><i
                                            class="fas fa-star"></i> Getting
                                    Started</a></li>
                            <li><a href="https://my.vatsim.net/learn" target="_blank"><i
                                            class="fas fa-school"></i> Training</a></li>
                            <li><a href="https://my.vatsim.net/virtual-airlines" target="_blank"><i
                                            class="fas fa-plane"></i> Virtual
                                    Airlines</a></li>
                            <li><a href="http://stats.vatsim.net/" target="_blank"><i class="fas fa-chart-line"></i>
                                    VATSIM Stats/Tracking</a></li>
                            <li class="divider"></li>
                            <li><a href="https://www.vatusa.net/forums/?action=calendar"><i class="fas fa-calendar"></i>
                                    Events Calendar</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">
                                <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">Other Resources</h5>
                            </li>
                            <li><a href="https://www.skyvector.com" target="_blank"><i class="fas fa-route"></i> Charts</a>
                            </li>
                            <li><a href="http://www.flightaware.com/statistics/ifr-route/" target="_blank"><i
                                            class="fas fa-map"></i> Routes</a>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false"><i class="fas fa-cloud"></i> Weather</a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://aviationweather.gov/data/metar/" target="_blank">METARs</a>
                                    </li>
                                    <li><a href="https://aviationweather.gov/data/taf/" target="_blank">TAFs</a></li>
                                    <li><a href="https://aviationweather.gov/data/pirep/" target="_blank">PIREPs</a>
                                    </li>
                                    <li><a href="http://weather.uwyo.edu/upperair/sounding.html" target="_blank">Balloon
                                            Sounding</a></li>
                                    <li><a href="https://aviationweather.gov/data/windtemp/" target="_blank">Winds
                                            Aloft</a></li>
                                    <li><a href="https://aviationweather.gov/gfa/#obs" target="_blank">Graphical
                                            Forecasts</a>
                                    </li>
                                    <li><a href="https://aviationweather.gov/gfa/#obs" target="_blank">Current
                                            Observations</a>
                                    </li>
                                    <li><a href="https://aviationweather.gov/gfa/#progchart" target="_blank">Prog
                                            Charts</a></li>
                                    <li><a href="https://www.faa.gov/air_traffic/weather/asos/" target="_blank">ASOS/AWOS
                                            Stations</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @if((Auth::check() && !Auth::user()->facility()->active && Auth::user()->facility != "ZHQ") || !Auth::check())
                        <li class="dropdown"><a href="/info/join">Join Us</a></li>
                    @endif
                    @if(!Auth::check())
                        <li><a href="/tmu/notices" target="_blank">N.T.O.S. - TMU Notices</a></li>
                    @else
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                                aria-expanded="false">
                                TMU Maps
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/tmu/notices" target="_blank">N.T.O.S. - TMU Notices</a></li>
                                <li class="divider"></li>
                                @foreach(\App\Models\tmu_facilities::where('parent',null)->orderBy('id', 'asc')->get() as $f)
                                    @if(\App\Models\tmu_facilities::where('parent', $f->id)->count() >= 1)
                                        <li class="dropdown-submenu">
                                            <a href="#" target="_blank" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button" aria-expanded="false">{{$f->id}} - {{$f->name}}</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/tmu/map/{{$f->id}}" target="_blank">{{$f->id}}
                                                        - {{$f->name}}</a></li>
                                                @foreach(\App\Models\tmu_facilities::where('parent', $f->id)->orderBy('id', 'asc')->get() as $sf)
                                                    <li><a href="/tmu/map/{{$sf->id}}" target="_blank">{{$sf->id}}
                                                            - {{$sf->name}}</a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li><a href="/tmu/map/{{$f->id}}" target="_blank">{{$f->id}} - {{$f->name}}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false">Support <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="https://status.vatusa.net">System Status</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url("/help/kb") }}"><i class="fas fa-question-circle"></i>
                                    Knowledgebase/FAQ</a></li>
                            <li><a href="{{ url("/help/ticket/new") }}"><i class="fas fa-life-ring"></i> Open New
                                    Ticket</a></li>
                            <li><a href="{{ url("/help/ticket/mine") }}"><i class="far fa-life-ring"></i> My
                                    Tickets</a></li>
                            @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTickets())
                                <li class="divider"></li>
                                <li class="dropdown-submenu"><a href="#" class="dropdown-toggle"
                                                                data-toggle="dropdown" role="button"
                                                                aria-expanded="false"><i
                                                class="fas fa-hands-helping"></i> Ticket Manager</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ url("/help/ticket/myassigned") }}">My Assigned
                                                Tickets</a></li>
                                        <li><a href="{{ url("/help/ticket/open") }}">Open Tickets</a></li>
                                        <li><a href="{{ url("/help/ticket/closed") }}">Closed Tickets</a></li>
                                        <li><a href="{{ url("/help/ticket/search") }}">Search Tickets</a></li>
                                    </ul>
                                </li>
                                @if (\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                                    <li><a href="{{ url("/help/kbe") }}">Knowledgebase Editor</a></li>
                                @endif
                            @endif
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    @if(Auth::check())
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                                aria-expanded="false">
                                <i class="fa fa-user"></i> My VATUSA<span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('my/profile') }}"><i class="fas fa-id-badge"></i> Profile</a>
                                </li>
                                @if(Auth::user()->selectionEligible())
                                    <li><a href="{{ url('my/select') }}"><i class="fas fa-star"></i> Join
                                            Facility</a>
                                    </li>
                                @elseif(Auth::user()->transferEligible())
                                    <li><a href="{{ url('my/transfer') }}"><i class="fas fa-exchange-alt"></i>
                                            Transfer Request</a></li>
                                @else
                                    <li><a href="{{ url("/my/profile") }}"><i class="fas fa-question-circle"></i>
                                            Why can I not join
                                            a facility or transfer?</a></li>
                                @endif
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ (env('APP_ENV', 'prod') == "dev") ? url("logout") : str_replace('api', 'login', \App\Classes\Helper::apiUrl()) . "/?logout" }}"><i
                                                class="fas fa-sign-out-alt"></i> Logout</a>
                                </li>
                            </ul>
                        </li>
                        @if(\App\Helpers\AuthHelper::authACL()->canUseActionsMenu())
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                   role="button" aria-expanded="false">
                                    <i class="fa fa-cogs"></i> Actions<span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">

                                    <!-- Facility -->
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                            <i class="fas fa-building"></i> Facility Actions
                                        </h5>
                                    </li>

                                    <!-- Facility Management [Mentor/Instructors/VATUSA/ATM/DATM/TA/WM] -->
                                    @if(\App\Helpers\AuthHelper::authACL()->canViewFacilityRoster())
                                        <li><a href="{{url("mgt/facility")}}">Facility Management</a></li>
                                    @endif

                                    <!-- TMU Management [ATM/DATM/TA/WM/FE] -->
                                    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTMU())
                                        <li><a href="{{url('mgt/tmu')}}">TMU Management</a></li>
                                    @endif

                                    <!-- Controllers -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                            <i class="fas fa-users-cog"></i> Controller Actions</h5>
                                    </li>

                                    <!-- Member Management [Mentor/Instructors/ATM/DATM/TA/VATUSA/WM] -->
                                    @if(\App\Helpers\AuthHelper::authACL()->canViewFacilityRoster())
                                        <li><a href="{{url("mgt/controller")}}">Member Management</a></li>
                                    @endif

                                    <!-- Submit Transfer Request [VATUSA] -->
                                    @if (\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                                        <li><a href="{{url("mgt/transfer") }}">Submit Transfer Request</a></li>
                                    @endif

                                    @if(\App\Helpers\AuthHelper::authACL()->canViewTrainingRecords())
                                        <!-- Training -->
                                        <li class="nav-divider"></li>
                                        <li class="dropdown-header">
                                            <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                                <i class="fas fa-chalkboard-teacher"></i> Training Actions</h5>
                                        </li>
                                        <li>
                                            <a href="{{ url("/mgt/facility/training/evals") }}">OTS Evaluations</a>
                                        </li>
                                        <!--This is exactly like the Training tab of records, but with OTS Evals. ARTCC select, position groups, and everything. -->
                                    @endif
                                    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilitySoloCertifications())
                                        <li>
                                            <a href="{{ url("mgt/solo") }}">Solo Certifications</a>
                                        </li>
                                    @endif

                                    <!-- Communication -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                            <i class="fas fa-broadcast-tower"></i> Communication Actions</h5>
                                    </li>

                                    @if(\App\Helpers\AuthHelper::authACL()->canSendBroadcastEmail())
                                        <li><a href="{{ url("mgt/mail/broadcast") }}">Broadcast</a></li>
                                    @endif
                                    @if(\App\Helpers\AuthHelper::authACL()->canManageFacilityTechConfig())
                                        <li><a href="{{ url("mgt/mail/welcome") }}">Facility Welcome Message</a></li>
                                    @endif

                                    <!-- Division -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                            <i class="fas fa-chart-bar"></i> Statistics
                                        </h5>
                                    </li>

                                    <!-- Division Statistics [All] -->
                                    <li><a href="{{ url("/mgt/stats") }}">Division Statistics</a></li>
                                    @if(\App\Helpers\AuthHelper::authACL()->canViewTrainingRecords())
                                        <!-- Training Statistics [INS/ATM/DATM/TA/VATUSA] -->
                                        <li>
                                            <a href="{{ url("/mgt/facility/training/stats") }}">Training Statistics</a>
                                        </li>
                                    @endif

                                    <!-- ACE Team/Division Staff Management [VATUSA] -->
                                    @if (\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                                    <!-- Division -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                            <i class="fas fa-city"></i> Division Actions</h5>
                                    </li>
                                        <li><a href="{{url("mgt/ace") }}">ACE Team Management</a></li>
                                        <li><a href="{{url("mgt/staff") }}">Division Staff Management</a></li>
                                        <li><a href="{{url("mgt/roles") }}">All Assigned Roles</a></li>
                                        <li><a href="{{url("mgt/policies") }}">Policies & Downloads</a></li>
                                </ul>
                                @endif
                            </li>
                        @endif

                    @else
                        <li><a href="#" id="login-link" data-action="{{ url('login') }}"><i class="fas fa-user"></i>
                                Login</a></li>
                    @endif
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
        <!--/.container-fluid -->
    </nav>
</div>
<div class="clear-fix"></div>
<style>
    .table {
        background-color: transparent !important;
    }
</style>
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> {!! session('error') !!}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">
            <strong><i class="fa fa-check"></i> Success!</strong> {!! session('success') !!}
        </div>
    @endif
    @if (isset($errors) && count($errors) > 0)
        <div class="alert alert-danger">
            <strong>There was an error processing your request, please correct the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@if(env('APP_ENV', 'dev') != 'prod')
    <div class="container">
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-triangle"></i> WARNING</strong> This is a development environment!!!
            While live, it does feature beta/alpha
            software that may not be functioning.
        </div>
    </div>
@endif
@if(env('APP_ENV') == 'alpha')
    <div class="container">
        <div class="alert alert-danger">
            <strong>WARNING</strong> This is an active development environment. Features are likely to be broken or in
            the active process of being changed.
        </div>
    </div>
@endif
@yield('content')
<footer>
    <div class="container">
        <hr>
        <p>Copyright &copy; 2016-{{ date("Y") }} VATUSA - United States Division, VATSIM. All
            rights reserved. Any and all content on this website are for use with the Virtual Air Traffic Simulation
            Network (VATSIM) and may not be used for real-world navigation or aviation purposes and doing so could be a
            violation of federal law.</p>
        <p>{!! \App\Classes\Helper::version() !!} - <a href="http://github.com/vatusa/current"><i
                        class="fab fa-github"></i> Open Source on GitHub</a> | <a href="/info/privacy"><i
                        class="fa fa-lock"></i> Privacy Policy</a></p>
    </div>
</footer>
<script
        src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
        integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script src="/js/bootbox.min.js"></script>
<script src="/js/vatusa.js"></script>
<script src="/js/bootstrap-formhelpers.js"></script>

@yield('scripts')
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-112506058-1"></script>
<script>
    window.dataLayer = window.dataLayer || []

    function gtag() {
        dataLayer.push(arguments)
    }

    gtag('js', new Date())

    gtag('config', 'UA-112506058-1')
</script>
</body>
</html>
