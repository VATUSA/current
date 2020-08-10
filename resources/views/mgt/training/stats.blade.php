@extends('layout')
@section('title', 'Training Statistics')
@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
@endpush
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading"><h5 class="panel-title"><i class="fas fa-chart-line"></i> Training Statistics
                    @if(!\App\Classes\RoleHelper::isVATUSAStaff())
                        - {{ \App\Classes\Helper::facShtLng($facility) }} @endif</h5>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                              data-toggle="tab"><i class="fas fa-home"></i> Summary</a>
                    </li>
                    <li role="presentation"><a href="#activity" aria-controls="activity" role="tab"
                                               data-toggle="tab"><i class="fas fa-users"></i> INS/MTR
                            Activity</a></li>
                    <li role="presentation"><a href="#evals" aria-controls="evals" role="tab"
                                               data-toggle="tab"><i class="fas fa-check-double"></i> OTS
                            Evaluations</a></li>
                    <li role="presentation"><a href="#records" aria-controls="records" role="tab"
                                               data-toggle="tab"><i
                                class="fa fa-list"></i> Training
                            Records</a></li>
                </ul>
                <div class="tab-content training-stat-content">
                    <div role="tabpanel" class="tab-pane active training-stat-section" id="home">
                        <h3 class="training-stat-section-header"><i class="fas fa-home"></i> Summary</h3>
                        <!-- Stacked Bar (By INS): Time per Month -->
                        <!-- Total HH:MM Training Time (period) -->
                        <!-- Pie: Instructor Time Distribution (period) -->
                        <!--Table: INS, MTRS: Training Time, Avg Session Time, Sessions Conducted -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body training-stat-static">
                                        {{ $totalTimeStr }}<br> {{ $totalSessions }} sessions
                                    </div>
                                    <div class="panel-footer">Total Session Time<br><em>last 30
                                            days</em></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body training-stat-static">
                                        {{ $passRate }}%<br><span
                                            class="text-success">Pass: <strong>{{ $numPass }}</strong></span> |
                                        <span class="text-danger">Fail: <strong>{{ $numFail }}</strong></span>
                                    </div>
                                    <div class="panel-footer">OTS Pass Rate<br><em>last 30 days</em></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body training-stat-static">
                                        {{ $avgTimeStr }}<br>{{ $avgSessions }} sessions
                                    </div>
                                    <div class="panel-footer">Average Time and Sessions Per Week<br><em>last
                                            30
                                            days</em></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane training-stat-section" id="activity">
                        <h3 class="training-stat-section-header"><i class="fas fa-users"></i> INS/MTR
                            Activity</h3>
                        <!-- Stacked Bar (By INS): Time per Month -->
                        <!-- Total HH:MM Training Time (period) -->
                        <!-- Pie: Instructor Time Distribution (period) -->
                        <!--Table: INS, MTRS: Training Time, Avg Session Time, Sessions Conducted -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="stacked-1"></canvas>
                                    </div>
                                    <div class="panel-footer">Hours per Month</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="pie-1"></canvas>
                                    </div>
                                    <div class="panel-footer">Time per Instructor<br><em>last 30
                                            days</em></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped" id="training-staff-list">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Training Time</th>
                                    <th>Avg. Session Time</th>
                                    <th>Sessions Conducted</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane training-stat-section" id="evals">
                        <h3 class="training-stat-section-header"><i class="fas fa-check-double"></i> OTS
                            Evaluations</h3>
                        <!-- Stacked Bar (by INS): Evals Conducted Per Month -->
                        <!--Pass Rate <br> # Passed # Failed (period)-->
                        <!-- Pie: # Conducted  by Form (period) -->
                        <!-- Table: Eval Forms:Pass Rate, Num Pass, Num Fail, Num Conducted, Button (Itemized Stats) -->
                        <!-- Table: OTS Evaluations: Date, Form Name, Student, Instructor, Result, # C/S/U (text in colors), Button (View) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-body training-stat-block">
                                        <canvas id="stacked-2"></canvas>
                                    </div>
                                    <div class="panel-footer">Evaluations Conducted per Month<br><em></em>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="pie-2"></canvas>
                                    </div>
                                    <div class="panel-footer">Completed Evaluations per Form<br><em>last 30
                                            days</em></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <h4 class="training-stat-section-header">Evaluation Forms</h4>
                            <table class="table table-striped" id="eval-forms">
                                <thead>
                                <tr>
                                    <th>Form Name</th>
                                    <th>Pass Rate</th>
                                    <th>Pass/Fail</th>
                                    <th>Conducted</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                            </table>
                            <h4 class="training-stat-section-header">Completed OTS Evaluations</h4>
                            <table class="table table-striped" id="completed-evals">
                                <thead>
                                <tr>
                                    <th>Exam Date</th>
                                    <th>Form Name</th>
                                    <th>Student</th>
                                    <th>Instructor</th>
                                    <th>Result</th>
                                    <th>C/S/U</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane training-stat-section" id="records">
                        <h3 class="training-stat-section-header"><i class="fa fa-list"></i> Training
                            Records</h3>
                        <!-- Pie: Position Distribution (period) -->
                        <!-- Avg. Sessions and Hours Per Week -->
                        <!-- Stacked Bar (By Postion): Records Completed Per Month -->
                        <!-- Table: Training Records -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="stacked-3"></canvas>
                                    </div>
                                    <div class="panel-footer">Records per Type<br><em>last 30 days</em>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="pie-3"></canvas>
                                    </div>
                                    <div class="panel-footer">Records per Month</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            Training Records Table
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script type="text/javascript">
      $(function () {
        if (document.location.hash)
          $('.nav-tabs li:not(.disabled) a[href=' + document.location.hash + ']').tab('show')

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
          history.pushState({}, '', e.target.hash)
        })
        $('#pos-types li a').click(function (e) {
          e.preventDefault()
          let target = $(this).data('controls')
          $('#training-content div[role="tabpanel"]#' + target).show()
          $('#training-content div[role="tabpanel"]:not(#' + target + ')').hide()
        })
      })
    </script>
@endpush