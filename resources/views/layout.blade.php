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
    <link href="/css/vatusa.css" rel="stylesheet">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/img/vatusa-512-darkblue.png">



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
      $.apiUrl = () => ("{{ ((env('APP_ENV', 'prod') == 'prod') ? "https://api.vatusa.net" : "https://api.vatusa.devel") }}")
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
                <img src="/img/logo-dark.png" class="logo"/>
                @if(Auth::check())
                    <div class="pull-right">
                        <div class="wb-usr">
                            <span class="grab"><strong>{{Auth::user()->fname.' '.Auth::user()->lname}}</strong></span>
                            <br>
                            <small><i class="fa fa-user"></i> {{Auth::user()->cid}} &nbsp; &mdash; &nbsp; <i
                                    class="fa fa-trophy"></i> {{\App\Classes\Helper::ratingShortFromInt(\Auth::user()->rating)}}
                                ({{Auth::user()->urating->long}})<br>
                                <i class="fa fa-star"></i> {{\App\Classes\RoleHelper::getUserRole(Auth::user()->cid, Auth::user()->facility)}}
                                - {{\App\Classes\Helper::facShtLng(Auth::user()->facility)}}
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
                    <li><a href="/">
                            Home
                        </a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Facilities
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="dropdown-header"><h5
                                    style="font-weight: bold; margin-top: 5px; margin-bottom: 5px;">Western Region</h5>
                            </li>
                            @foreach(\App\Facility::where(['active' => 1, 'region' => 7])->orderby('name', 'ASC')->get() as $f)
                                <li><a href="{{$f->url}}" target="_blank">{{$f->name}}</a></li>
                            @endforeach
                            <li class="nav-divider"></li>
                            <li class="dropdown-header"><h5
                                    style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">Southern Region</h5>
                            </li>
                            @foreach(\App\Facility::where(['active' => 1, 'region' => 8])->orderby('name', 'ASC')->get() as $f)
                                <li><a href="{{$f->url}}" target="_blank">{{$f->name}}</a></li>
                            @endforeach
                            <li class="nav-divider"></li>
                            <li class="dropdown-header"><h5
                                    style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">Northeastern
                                    Region</h5></li>
                            @foreach(\App\Facility::where(['active' => 1, 'region' => 9])->orderby('name', 'ASC')->get() as $f)
                                <li><a href="{{$f->url}}" target="_blank">{{$f->name}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li><a href="https://forums.vatusa.net">Forums</a></li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Division Info <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{secure_url('info/policies')}}">Policies</a></li>
                            <li><a href="{{secure_url("cbt")}}">Computer Based Training (CBT)</a></li>
                            <li><a href="https://forums.vatusa.net/?action=calendar">Events Calendar</a></li>
                            <li><a href="{{secure_url('info/members')}}">Members and Staff</a></li>
                            <li><a href="/info/solo">Solo Certs</a></li>
                            <li><a href="{{secure_url('info/ace')}}">ACE Team</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                            aria-expanded="false">
                            Pilot Tools
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="https://www.vatsim.net/pilots/getting-started" target="_blank">Getting
                                    Started</a></li>
                            <li><a href="https://www.vatusa.net/forums/?action=calendar">Events Calendar</a></li>
                            <li><a href="http://www.vatsim.net/pilots/training" target="_blank">Training</a></li>
                            <li><a href="http://www.vatsim.net/pilots/virtual-airlines" target="_blank">Virtual
                                    Airlines</a></li>
                            <li><a href="https://www.skyvector.com" target="_blank">Charts</a></li>
                            <li><a href="http://www.flightaware.com/statistics/ifr-route/" target="_blank">Routes</a>
                            </li>
                            <li><a href="http://stats.vatsim.net/" target="_blank">VATSIM Stats/Tracking</a></li>
                            <li class="dropdown-submenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">Weather</a>
                                <ul class="dropdown-menu">
                                    <li><a href="https://www.aviationweather.gov/metar" target="_blank">METARs</a></li>
                                    <li><a href="https://www.aviationweather.gov/taf" target="_blank">TAFs</a></li>
                                    <li><a href="https://www.aviationweather.gov/adds/pireps" target="_blank">PIREPs</a>
                                    </li>
                                    <li><a href="http://weather.uwyo.edu/upperair/sounding.html" target="_blank">Balloon
                                            Sounding</a></li>
                                    <li><a href="https://aviationweather.gov/windtemp" target="_blank">Winds
                                            Aloft</a></li>
                                    <li><a href="http://digital.weather.gov" target="_blank">Graphical Forecasts</a>
                                    </li>
                                    <li><a href="https://www.aviationweather.gov/progchart" target="_blank">Prog
                                            Charts</a></li>
                                    <li><a href="https://www.aviationweather.gov/satellite" target="_blank">Satellite
                                            Imagery</a></li>
                                    <li><a href="https://www.aviationweather.gov/radar" target="_blank">Radar</a></li>
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
                                @foreach(\App\tmu_facilities::where('parent',null)->orderBy('id', 'asc')->get() as $f)
                                    @if(\App\tmu_facilities::where('parent', $f->id)->count() >= 1)
                                        <li class="dropdown-submenu">
                                            <a href="#" target="_blank" class="dropdown-toggle" data-toggle="dropdown"
                                               role="button" aria-expanded="false">{{$f->id}} - {{$f->name}}</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="/tmu/map/{{$f->id}}" target="_blank">{{$f->id}}
                                                        - {{$f->name}}</a></li>
                                                @foreach(\App\tmu_facilities::where('parent', $f->id)->orderBy('id', 'asc')->get() as $sf)
                                                    <li><a href="/tmu/map/{{$sf->id}}" target="_blank">{{$sf->id}}
                                                            - {{$sf->name}}</a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li><a href="/tmu/map/{{$f->id}}" target="_blank">{{$f->id}} - {{$f->name}}</a></li>
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
                            <li><a href="/help/kb">Knowledgebase/FAQ</a></li>
                            <li><a href="/help/ticket/new">Open New Ticket</a></li>
                            <li><a href="/help/ticket/mine">My Tickets</a></li>
                            @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isVATUSAStaff())
                                <li class="dropdown-submenu"><a href="#" class="dropdown-toggle"
                                                                data-toggle="dropdown" role="button"
                                                                aria-expanded="false">Ticket Manager</a>
                                    <ul class="dropdown-menu">
                                        <li><a href="/help/ticket/myassigned">My Assigned Tickets</a></li>
                                        <li><a href="/help/ticket/open">Open Tickets</a></li>
                                        <li><a href="/help/ticket/closed">Closed Tickets</a></li>
                                        <li><a href="/help/ticket/search">Search Tickets</a></li>
                                    </ul>
                                </li>
                                @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                    <li><a href="/help/kbe">Knowledgebase Editor</a></li>
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
                                <li><a href="{{secure_url('my/profile')}}">Profile</a></li>
                                @if(!Auth::user()->selectionEligible() && !Auth::user()->transferEligible())
                                    <li><a href="/my/profile">Why can I not join a facility?</a></li>
                                @endif
                                @if(Auth::user()->facility()->active || Auth::user()->facility == "ZHQ" || (Auth::user()->transferEligible() && !Auth::user()->selectionEligible()))
                                    <li><a href="{{secure_url('my/transfer')}}">Transfer Request</a></li>
                                @endif
                                @if (Auth::user()->selectionEligible())
                                    <li><a href="{{secure_url('my/select')}}">Join Facility</a></li>
                                @endif
                                <li><a href="{{secure_url('exam')}}">Exam Center</a></li>
                                <li><a href="{{secure_url("cbt")}}">Computer Based Training (CBT)</a></li>
                                @if(Auth::user()->flag_needbasic)
                                    <li role="separator" class="divider"></li>
                                    <li><a href="/my/assignbasic">Request Basic ATC Exam</a></li>
                                @endif
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ (env('APP_ENV', 'prod') == "dev") ? url("logout") : "//login.vatusa.net/?logout" }}">Logout</a>
                                </li>
                            </ul>
                        </li>

                        @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isMentor())
                            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                                    role="button" aria-expanded="false">
                                    <i class="fa fa-cogs"></i> Actions<span class="caret"></span></a>
                                
                                    <ul class="dropdown-menu" role="menu">

                                    <!-- Facility -->
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                        <i class="fas fa-building"></i> Facility Actions</h5>
                                    </li>

                                        <!-- Facility Management [Mentor/Instructors/VATUSA/ATM/DATM/TA/WM] -->
                                        @if(\App\Classes\RoleHelper::isMentor() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "WM"))
                                            <li><a href="{{secure_url("mgt/facility")}}">Facility Management</a></li>
                                        @endif
                                        
                                        <!-- TMU Management [ATM/DATM/TA/WM/FE] -->
                                        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "WM") || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "FE"))
                                            <li><a href="{{secure_url('mgt/tmu')}}">TMU Map Management</a></li>
                                        @else
                                            <li><a href="{{secure_url('mgt/tmu')}}">TMU Notices Management</a></li>
                                        @endif

                                    <!-- Controllers -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                        <i class="fas fa-users-cog"></i> Controller Actions</h5>
                                    </li>

                                        <!-- Member Management [Mentor/Instructors/ATM/DATM/TA/VATUSA/WM] -->
                                        @if(\App\Classes\RoleHelper::isMentor() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::hasRole(\Auth::user()->cid, \Auth::user()->facility, "WM"))
                                            <li><a href="{{secure_url("mgt/controller")}}">Member Management</a></li>
                                        @endif

                                        <!-- Submit Transfer Request [VATUSA] -->
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                            <li><a href="{{secure_url("mgt/err") }}">Submit Transfer Request</a></li>
                                        @endif

                                    <!-- Training --> 
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                        <i class="fas fa-chalkboard-teacher"></i> Training Actions</h5>
                                    </li>

                                        <!-- Exam Management [Instructors/ATM/DATM/TA] -->
                                        @if(\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff())
                                            <li><a href="{{secure_url("exam")}}">Exam Management</a></li>
                                        @endif

                                        <!-- CBT Editor [VATUSA/ATM/DATM/TA] -->
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isAcademyStaff())
                                            <li><a href="{{secure_url("cbt/editor") }}">CBT Editor</a></li>
                                        @endif
                                        
                                        <!-- Checklists Management [VATUSA] -->
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                            <li><a href="{{ secure_url("/mgt/checklists") }}">Checklists Management</a></li>
                                        @endif
                                    

                                    <!-- Communication --> 
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                        <i class="fas fa-broadcast-tower"></i> Communication Actions</h5>
                                    </li>

                                        <!-- Email Management [ATM/DATM/TA/VATUSA] -->
                                        @if(\App\Classes\RoleHelper::isFacilityStaff())
                                            <li><a href="{{secure_url("mgt/mail") }}">Email Management</a></li>
                                        @endif

                                        <!-- iDENT App Management [ATM/DATM/TA/VATUSA] -->
                                        @if (\App\Classes\RoleHelper::isFacilitySeniorStaff())
                                            <li><a href="{{url("mgt/app/push")}}">iDENT App Management</a></li>
                                        @endif

                                    <!-- Division -->
                                    <li class="nav-divider"></li>
                                    <li class="dropdown-header">
                                        <h5 style="font-weight: bold; margin-top: 0; margin-bottom: 5px;">
                                        <i class="fas fa-city"></i> Division Actions</h5>
                                    </li>

                                        <!-- ACE Team/Division Staff Management [VATUSA] -->
                                        @if (\App\Classes\RoleHelper::isVATUSAStaff())
                                            <li><a href="{{secure_url("mgt/ace") }}">ACE Team Management</a></li>
                                            <li><a href="{{secure_url("mgt/staff") }}">Division Staff Management</a></li>
                                        @endif

                                        <!-- Division Statistics [All] -->
                                        <li><a href="{{ secure_url("/stats") }}">Division Statistics</a></li>

                                        <!-- Solo Certifications [Instructors/ATM/DATM/TA/VATUSA] -->
                                        @if(\App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isVATUSAStaff())
                                            <li><a href="{{ secure_url("mgt/solo") }}">Solo Certifications</a></li>
                                        @endif

                                    </ul>
                                </li>
                            @endif


                    @else
                        <li><a href="#" id="login-link" data-action="{{ url('login') }}"><i class="fa fa-user"></i>
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
@if(env('APP_ENV') == 'dev')
    <div class="container">
        <div class="alert alert-danger">
            <strong>WARNING</strong> This is a development environment!!! While live, it does feature beta/alpha
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

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
<script src="/js/bootbox.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/vatusa.js"></script>
<script src="/js/bootstrap-formhelpers.js"></script>
@yield('scripts')
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-112506058-1"></script>
<script>
  window.dataLayer = window.dataLayer || []

  function gtag () {dataLayer.push(arguments)}

  gtag('js', new Date())

  gtag('config', 'UA-112506058-1')
</script>
</body>
</html>
