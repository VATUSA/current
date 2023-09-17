@extends('layout')
@section('title', 'Training Statistics')
@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
@endpush
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><i class="fas fa-chart-line"></i> Training Statistics
                    @if(!\App\Classes\RoleHelper::isFacilitySeniorStaff())
                        - {{ \App\Classes\Helper::facShtLng($facility) }}
                    @else -
                    <form class="form-inline" action="{{ url("mgt/facility/training/stats") }}#training"
                          method="POST"
                          id="training-artcc-select-form" style="display: inline;">
                        <div class="form-group">
                            <select class="form-control" id="tng-artcc-select" autocomplete="off" name="facility">
                                <option value="0" @if(!$facility) selected @endif>All Facilities</option>
                                @foreach($facilities as $fac)
                                    <option value="{{ $fac->id }}"
                                            @if($facility == $fac->id) selected @endif>{{ $fac->id }} - {{ $fac->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form> @endif</h5>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                              data-toggle="tab"><i class="fas fa-home"></i> Summary</a>
                    </li>
                    <li role="presentation"
                        @if($facility && empty($timePerInstructorData['datasets'][0]['data'])) class="disabled"
                        rel="tooltip" title="No data in last 30 days" @endif><a href="#activity"
                                                                                aria-controls="activity" role="tab"
                                                                                @if(!($facility && empty($timePerInstructorData['datasets'][0]['data']))) data-toggle="tab" @endif><i
                                class="fas fa-users"></i> INS/MTR
                            Activity</a></li>
                    <li role="presentation"><a href="#evals" aria-controls="evals" role="tab"
                                               data-toggle="tab"><i
                                class="fas fa-check-double"></i>
                            OTS
                            Evaluations</a></li>
                    <li role="presentation"
                        @if($facility && empty($recordsPerTypeData['datasets'][0]['data'])) class="disabled"
                        rel="tooltip"
                        title="No data in last 30 days" @endif><a href="#records" aria-controls="records" role="tab"
                                                                  @if(!($facility && empty($recordsPerTypeData['datasets'][0]['data']))) data-toggle="tab" @endif><i
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
                                        {{ $sumTotalTimeStr }}<br> {{ $sumTotalSessions }} sessions
                                    </div>
                                    <div class="panel-footer">Total Session Time<br><em>last 30
                                            days</em></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body training-stat-static">
                                        @if(!$sumNumPass && !$sumNumFail) <em>No OTS
                                            Evaluations</em> @else {{ $sumPassRate }}% @endif<br><span
                                            class="text-success">Pass: <strong>{{ $sumNumPass }}</strong></span> |
                                        <span class="text-danger">Fail: <strong>{{ $sumNumFail }}</strong></span>
                                    </div>
                                    <div class="panel-footer">OTS Pass Rate<br><em>last 30 days</em></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body training-stat-static">
                                        {{ $sumAvgTimeStr }}<br>{{ $sumAvgSessions }} sessions
                                    </div>
                                    <div class="panel-footer">Average Time and Sessions Per Training Staff<br>
                                        <em>last 30 days</em></div>
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
                            <div class="col-md-7" @if(!$facility) style="float: none; margin:0 auto;" @endif>
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="stacked-1"></canvas>
                                    </div>
                                    <div class="panel-footer">Hours per Month<br><em>Last 6 months</em></div>
                                </div>
                            </div>
                            @if($facility)
                                <div class="col-md-5">
                                    <div class="panel panel-default training-stat-block">
                                        <div class="panel-body">
                                            <canvas id="pie-1" height="220px"></canvas>
                                        </div>
                                        <div class="panel-footer">Time per Instructor<br><em>last 30
                                                days</em></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <table class="table table-striped" id="training-staff-list">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Hours</th>
                                    <th>Sessions Conducted</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($activityTableData as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['role'] === "INS" ? "Instructors" : "Mentors" }}</td>
                                        <td>
                                            @php $c = 0; @endphp
                                            @foreach ($row['hours'] as $i => $v)
                                                <strong>Last {{ $i }} days:</strong> {!! $v !!}
                                                @if(++$c != count($row['hours'])) <br>@endif
                                            @endforeach
                                        </td>
                                        <td>@php $c = 0; @endphp
                                            @foreach ($row['sessions'] as $i => $v)
                                                <strong>Last {{ $i }} days:</strong> {!! $v !!}
                                                @if(++$c != count($row['sessions'])) <br>@endif
                                            @endforeach</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane training-stat-section" id="evals">
                        <h3 class="training-stat-section-header"><i class="fas fa-check-double"></i> OTS
                            Evaluations</h3>
                        @if($facility)
                            <br>
                            <div class="training-stat-section-header"> Charts Mode:
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-info active ots-charts-mode-input-label">
                                        <input type="radio" name="mode" value="1"
                                               autocomplete="off" class="ots-charts-mode" checked>
                                        By Form
                                    </label>
                                    <label class="btn btn-default ots-status-input-label">
                                        <input type="radio" name="mode" value="2"
                                               class="ots-charts-mode" autocomplete="off">By Instructor
                                    </label>
                                </div>
                            </div>
                    @endif
                    <!-- Stacked Bar (by INS): Evals Conducted Per Month -->
                        <!--Pass Rate <br> # Passed # Failed (period)-->
                        <!-- Pie: # Conducted  by Form (period) -->
                        <!-- Table: Eval Forms:Pass Rate, Num Pass, Num Fail, Num Conducted, Button (Itemized Stats) -->
                        <!-- Table: OTS Evaluations: Date, Form Name, Student, Instructor, Result, # C/S/U (text in colors), Button (View) -->

                        <div class="row">
                            <div class="col-md-7">
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="stacked-2"></canvas>
                                    </div>
                                    <div class="panel-footer">Evaluations Conducted per Month<br><em>last 6
                                            months</em>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($evalsPerFormData['datasets'][0]['data']))
                                <div class="col-md-5">
                                    <div class="panel panel-default training-stat-block">
                                        <div class="panel-body">
                                            <canvas id="pie-2" height="220px"></canvas>
                                        </div>
                                        <div class="panel-footer">Completed Evaluations per <span
                                                id="ots-mode">Form</span><br><em>last 30
                                                days</em></div>
                                    </div>
                                </div>
                            @endif
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
                                <tbody>
                                @foreach($evalFormsTable as $form)
                                    <tr>
                                        <td>{{ $form['name'] }} @if(strlen($form['sparkline']))<span
                                                class="sparkline-tri"
                                                values="{{ $form['sparkline'] }}"></span> @endif</td>
                                        <td>@php $c = 0; @endphp
                                            @foreach ($form['passRate'] as $i => $v)
                                                <strong>Last {{ $i }} days:</strong> {!! $v !!}%
                                                @if(++$c != count($form['passRate'])) <br>@endif
                                            @endforeach</td>
                                        <td>@php $c = 0; @endphp
                                            @foreach ($form['numPass'] as $i => $v)
                                                <strong>Last {{ $i }} days:</strong> <span
                                                    class="text-success">{!! $v !!}</span>/<span
                                                    class="text-danger">{!! $form['numFail'][$i] !!}</span>
                                                @if(++$c != count($form['numPass'])) <br>@endif
                                            @endforeach</td>
                                        <td>@php $c = 0; @endphp
                                            @foreach ($form['numConducted'] as $i => $v)
                                                <strong>Last {{ $i }} days:</strong> {!! $v !!}
                                                @if(++$c != count($form['numConducted'])) <br>@endif
                                            @endforeach</td>
                                        <td>
                                            <a href="{{ url("mgt/facility/training/eval/" . $form['id'] . "/stats") }}">
                                                <button class="btn btn-primary"><i class="fas fa-bullseye"></i> Itemized
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row text-center">
                                <a href="{{ url("/mgt/facility/training/evals") }}">
                                    <button class="btn btn-success"><i class="fas fa-external-link-alt"></i> View
                                        Completed Evaluations
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane training-stat-section" id="records">
                        <h3 class="training-stat-section-header"><i class="fa fa-list"></i> Training
                            Records</h3>
                        <!-- Pie: Position Distribution (period) -->
                        <!-- Avg. Sessions and Hours Per Week -->
                        <!-- Stacked Line (By Postion): Records Completed Per Month -->
                        <!-- Table: Training Records Table and sidebar -->
                        <div class="row">
                            <div class="col-md-7" @if(!$facility) style="float: none; margin: 0 auto;" @endif>
                                <div class="panel panel-default training-stat-block">
                                    <div class="panel-body">
                                        <canvas id="line-1"></canvas>
                                    </div>
                                    <div class="panel-footer">Records per Month<br><em>last 6 months</em></div>
                                </div>
                            </div>
                            @if($facility)
                                <div class="col-md-5">
                                    <div class="panel panel-default training-stat-block">
                                        <div class="panel-body">
                                            <canvas id="pie-3" height="220px"></canvas>
                                        </div>
                                        <div class="panel-footer">Records per Type<br><em>last 30 days</em>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            @include('mgt.training.viewRecords.training')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="{{ secure_asset("js/jquery.sparkline.js") }}"></script>
    <script type="text/javascript">
      $(function () {
        if (document.location.hash)
          $('.nav-tabs li:not(.disabled) a[href=' + document.location.hash + ']').tab('show')
        $('.sparkline').sparkline('html', {enableTagOptions: true, disableHiddenCheck: true})
        $('.sparkline-tri').sparkline('html', {
          type               : 'tristate',
          tooltipFormat      : ' <span style="color: @{{color}}">&#9679;</span> @{{value:result}}</span>',
          tooltipValueLookups: {result: {'-1': 'Fail', '1': 'Pass'}}
        })
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
          $('.sparkline').sparkline('html', {enableTagOptions: true, disableHiddenCheck: true})
          $('.sparkline-tri').sparkline('html', {
            type               : 'tristate',
            tooltipFormat      : ' <span style="color: @{{color}}">&#9679;</span> @{{value:result}}</span>',
            tooltipValueLookups: {result: {'-1': 'Fail', '1': 'Pass'}}
          })
          $.sparkline_display_visible()
          renderCharts()
          history.pushState({}, '', e.target.hash)
        })
        $('#pos-types li a').click(function (e) {
          e.preventDefault()
          let target = $(this).data('controls')
          $('#training-content div[role="tabpanel"]#' + target).show()
          $('#training-content div[role="tabpanel"]:not(#' + target + ')').hide()
        })

        let stacked1 = {}, stacked2 = {}, pie1 = {}, pie2 = {}, pie3 = {}, line1 = {}
        const renderCharts = () => {
          if (typeof stacked1.destroy === 'function') {
            stacked1.destroy()
            stacked2.destroy()
            pie1.destroy()
            pie2.destroy()
            pie3.destroy()
            line1.destroy()
          }

          //INS/MTR Activity
          stacked1 = new Chart($('#stacked-1'), {
            type   : 'bar',
            data   : {!! json_encode($hoursPerMonthData) !!},
            options: {
              scales: {
                xAxes: [{
                  stacked: true
                }],
                yAxes: [{
                  stacked: true,
                  ticks  : {
                    min: 0
                  }
                }]
              }
              //axes stacked
            }
          })
          pie1 = new Chart($('#pie-1'), {
            type   : 'pie',
            data   : {!! json_encode($timePerInstructorData) !!},
            options: {
              legend: {
                position: 'right'
              }
            }
          })

          //OTS Evalutations
          renderOtsEvalCharts(1)

          //Training Records
          line1 = new Chart($('#line-1'), {
            type   : 'line',
            data   : {!! json_encode($recordsPerMonthData) !!},
            options: {
              scales: {
                yAxes: [{
                  ticks: {
                    stacked: false,
                    min    : 0
                  }
                }]
              }
            }
          })
          pie3 = new Chart($('#pie-3'), {
            type   : 'pie',
            data   : {!! json_encode($recordsPerTypeData) !!},
            options: {
              legend: {
                position: 'right'
              }
            }
          })

        }
        const renderOtsEvalCharts = mode => {
          if (typeof stacked2.destroy === 'function') {
            stacked2.destroy()
            pie2.destroy()
          }
          if (mode == 1) {
            stacked2 = new Chart($('#stacked-2'), {
              type   : 'bar',
              data   : {!! json_encode($evalsPerMonthData) !!},
              options: {
                scales: {
                  xAxes: [{
                    stacked: true
                  }],
                  yAxes: [{
                    stacked: true,
                    ticks  : {
                      min: 0
                    }
                  }]
                }
                //axes stacked
              }
            })
            pie2 = new Chart($('#pie-2'), {
              type: 'pie',
              data: {!! json_encode($evalsPerFormData) !!}
            })
          } else {
            stacked2 = new Chart($('#stacked-2'), {
              type   : 'bar',
              data   : {!! json_encode($evalsPerMonthDataIns) !!},
              options: {
                scales: {
                  xAxes: [{
                    stacked: true
                  }],
                  yAxes: [{
                    stacked: true,
                    ticks  : {
                      min: 0
                    }
                  }]
                }
                //axes stacked
              }
            })
            pie2 = new Chart($('#pie-2'), {
              type   : 'pie',
              data   : {!! json_encode($evalsPerFormDataIns) !!},
              options: {
                legend: {
                  position: 'right'
                }
              }
            })
          }
        }
        $('.ots-charts-mode').change(function () {
          $('.ots-charts-mode').parent().attr('class', 'btn btn-default ots-charts-mode-input-label')
          let parent = $(this).parent()
          switch (parseInt($(this).val())) {
            case 1:
              parent.removeClass('btn-default').addClass('btn-info')
              $('#ots-mode').text('form')
              renderOtsEvalCharts(1)
              break
            case 2:
              parent.removeClass('btn-default').addClass('btn-success')
              $('#ots-mode').text('Instructor')
              renderOtsEvalCharts(2)
              break
          }
        })
        $('#training-staff-list').DataTable({
          responsive  : true,
          autoWidth   : false,
          lengthMenu  : [25, 50, 100],
          pageLength  : 25,
          columnDefs  : [{
            visible: false,
            targets: 1
          }],
          order       : [0, 'asc'],
          orderFixed  : [1, 'asc'],
          drawCallback: function (settings) {
            let api = this.api()
            let rows = api.rows({page: 'current'}).nodes()
            let last = null

            api.column(1, {page: 'current'}).data().each(function (group, i) {
              if (last !== group) {
                $(rows).eq(i).before(
                  '<tr class="group"><td colspan="6"><strong>' + group + '</strong></td></tr>'
                )

                last = group
              }
            })
            $('.sparkline').sparkline('html', {enableTagOptions: true, disableHiddenCheck: true})
            $('.sparkline-tri').sparkline('html', {
              type               : 'tristate',
              tooltipFormat      : ' <span style="color: @{{color}}">&#9679;</span> @{{value:result}}</span>',
              tooltipValueLookups: {result: {'-1': 'Fail', '1': 'Pass'}}
            })
            $.sparkline_display_visible()
          }
        })
        renderCharts()
      })
    </script>
@endpush
