@extends('layout')
@section('title', 'Division Statistics')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Division Statistics
                </h3>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#overview" aria-controls="home" role="tab"
                                                                  data-toggle="tab">Overview</a></li>
                        <li role="presentation"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a>
                        </li>
                        <li role="presentation"><a href="#exams" aria-controls="trends" role="tab" data-toggle="tab">Exams</a>
                        </li>
                        <li role="presentation"><a href="#trends" aria-controls="trends" role="tab" data-toggle="tab">Trends</a>
                        </li>
                        <li role="presentation"><a href="#exports" aria-controls="exports" role="tab"
                                                   data-toggle="tab">Exports</a></li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="overview">
                            <table border="0" style="width:100%;" class="table table-responsive">
                                <tr>
                                    <td colspan="8">Total Active Members</td>
                                    <td>{{$controllersCount['ZAE'] + $regions[5] + $regions[6] + $regions[7]+ + $regions[8]}}</td>
                                </tr>
                                <tr>
                                    <td colspan="8">Academy Assigned</td>
                                    <td>{{$controllersCount['ZAE']}}</td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="background: #002868; color: #fff; font-weight: bold;">Western
                                        Region (USA8)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"
                                        style="background: #002868; color: #fff; font-weight: bold;">{{($us8==null)?"Vacant":$us8->user()->first()->fullname()}}</td>
                                    <td style="background: #002868; color: #fff; font-weight: bold; text-align: right">{{$regions[8]}}</td>
                                </tr>
                                <tr style="background: #cccccc">
                                    <td>FacID</td>
                                    <td>ATM</td>
                                    <td>DATM</td>
                                    <td>TA</td>
                                    <td>EC</td>
                                    <td>FE</td>
                                    <td>WM</td>
                                    <td>Transfers</td>
                                    <td>Total</td>
                                </tr>
                                @foreach ($west as $fac)
                                    <tr>
                                        <td>{{$fac->id}}</td>
                                        <td>{{$atms[$fac->id]}}</td>
                                        <td>{{$datms[$fac->id]}}</td>
                                        <td>{{$tas[$fac->id]}}</td>
                                        <td>{{$ecs[$fac->id]}}</td>
                                        <td>{{$fes[$fac->id]}}</td>
                                        <td>{{$wms[$fac->id]}}</td>
                                        <td>{{$transfersPending[$fac->id]}}</td>
                                        <td>{{$controllersCount[$fac->id]}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="9" style="background: #002868; color: #fff; font-weight: bold;">Midwestern Region (USA6)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"
                                        style="background: #002868; color: #fff; font-weight: bold;">{{($us6==null)?"Vacant":$us6->user()->first()->fullname()}}</td>
                                    <td style="background: #002868; color: #fff; font-weight: bold; text-align: right">{{$regions[6]}}</td>
                                </tr>
                                <tr style="background: #cccccc">
                                    <td>FacID</td>
                                    <td>ATM</td>
                                    <td>DATM</td>
                                    <td>TA</td>
                                    <td>EC</td>
                                    <td>FE</td>
                                    <td>WM</td>
                                    <td>Transfers</td>
                                    <td>Total</td>
                                </tr>
                                @foreach ($midwest as $fac)
                                    <tr>
                                        <td>{{$fac->id}}</td>
                                        <td>{{$atms[$fac->id]}}</td>
                                        <td>{{$datms[$fac->id]}}</td>
                                        <td>{{$tas[$fac->id]}}</td>
                                        <td>{{$ecs[$fac->id]}}</td>
                                        <td>{{$fes[$fac->id]}}</td>
                                        <td>{{$wms[$fac->id]}}</td>
                                        <td>{{$transfersPending[$fac->id]}}</td>
                                        <td>{{$controllersCount[$fac->id]}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="9" style="background: #002868; color: #fff; font-weight: bold;">Northeastern Region (USA7)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"
                                        style="background: #002868; color: #fff; font-weight: bold;">{{($us7==null)?"Vacant":$us7->user()->first()->fullname()}}</td>
                                    <td style="background: #002868; color: #fff; font-weight: bold; text-align: right">{{$regions[7]}}</td>
                                </tr>
                                <tr style="background: #cccccc">
                                    <td width="14%">FacID</td>
                                    <td width="13%">ATM</td>
                                    <td width="13%">DATM</td>
                                    <td width="13%">TA</td>
                                    <td width="13%">EC</td>
                                    <td width="13%">FE</td>
                                    <td width="13%">WM</td>
                                    <td width="2%">Transfers</td>
                                    <td width="3%">Total</td>
                                </tr>
                                @foreach ($northeast as $fac)
                                    <tr>
                                        <td>{{$fac->id}}</td>
                                        <td>{{$atms[$fac->id]}}</td>
                                        <td>{{$datms[$fac->id]}}</td>
                                        <td>{{$tas[$fac->id]}}</td>
                                        <td>{{$ecs[$fac->id]}}</td>
                                        <td>{{$fes[$fac->id]}}</td>
                                        <td>{{$wms[$fac->id]}}</td>
                                        <td>{{$transfersPending[$fac->id]}}</td>
                                        <td>{{$controllersCount[$fac->id]}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="9" style="background: #002868; color: #fff; font-weight: bold;">Southern Region (USA5)
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8"
                                        style="background: #002868; color: #fff; font-weight: bold;">{{($us5==null)?"Vacant":$us5->user()->first()->fullname()}}</td>
                                    <td style="background: #002868; color: #fff; font-weight: bold; text-align: right">{{$regions[5]}}</td>
                                </tr>
                                <tr style="background: #cccccc">
                                    <td width="14%">FacID</td>
                                    <td width="13%">ATM</td>
                                    <td width="13%">DATM</td>
                                    <td width="13%">TA</td>
                                    <td width="13%">EC</td>
                                    <td width="13%">FE</td>
                                    <td width="13%">WM</td>
                                    <td width="2%">Transfers</td>
                                    <td width="3%">Total</td>
                                </tr>
                                @foreach ($south as $fac)
                                    <tr>
                                        <td>{{$fac->id}}</td>
                                        <td>{{$atms[$fac->id]}}</td>
                                        <td>{{$datms[$fac->id]}}</td>
                                        <td>{{$tas[$fac->id]}}</td>
                                        <td>{{$ecs[$fac->id]}}</td>
                                        <td>{{$fes[$fac->id]}}</td>
                                        <td>{{$wms[$fac->id]}}</td>
                                        <td>{{$transfersPending[$fac->id]}}</td>
                                        <td>{{$controllersCount[$fac->id]}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="details"><br>
                            <div class="row">
                                <div class="col-md-12">
                                    Facility: <select id="detailfacilityselect">
                                        <option value="0">Select Facility</option>
                                        <option value="overview">Overview of Division</option>
                                        @foreach(\App\Models\Facility::where('active',1)->orderBy("name")->get() as $detfacility)
                                            <option value="{{$detfacility->id}}">{{$detfacility->name}}</option>
                                        @endforeach
                                    </select> <span id="detailprocessing"
                                                    style="display: none;">Processing, please wait</span>
                                </div>
                            </div>
                            <div id="detailcontainer" style="display: none;">
                                <div id="chartContainer" style="height: 600px;" class="span10">
                                </div>
                                <div class="row">
                                    <table id="detailcontainertable" class="table table-striped span12">
                                        <tr>
                                            <td>Total</td>
                                            <td><span id="detailtotal"></span></td>
                                        </tr>
                                        <tr>
                                            <td>OBS</td>
                                            <td><span id="detailobs"></span></td>
                                        </tr>
                                        <tr>
                                            <td>OBS >30 days</td>
                                            <td><span id="detailobsg30"></span></td>
                                        </tr>
                                        <tr>
                                            <td>S1</td>
                                            <td><span id="details1"></span></td>
                                        </tr>
                                        <tr>
                                            <td>S2</td>
                                            <td><span id="details2"></span></td>
                                        </tr>
                                        <tr>
                                            <td>S3</td>
                                            <td><span id="details3"></span></td>
                                        </tr>
                                        <tr>
                                            <td>C1-C3</td>
                                            <td><span id="detailc1"></span></td>
                                        </tr>
                                        <tr>
                                            <td>I1+</td>
                                            <td><span id="detaili1"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="exams"><br>
                            <div class="row">
                                <div class="col-md-12">
                                    Facility: <select id="examfacilityselect">
                                        <option value="0">Select Facility</option>
                                        @foreach(\App\Models\Facility::where('active',1)->orWhere("id", "ZAE")->orderBy("name")->get() as $detfacility)
                                            <option value="{{$detfacility->id}}">{{$detfacility->name}}</option>
                                        @endforeach
                                    </select> Year: <select id="examYearSelect">
                                        <option value="all">All</option>
                                        @for($year = 2015; $year <= date("Y"); $year++)
                                            <option{{ ($year == date("Y")) ? ' selected="true"' : ''}}>{{$year}}
                                            </option>
                                        @endfor
                                    </select> Month: <select id="examMonthSelect">
                                        <option value="all">All</option>
                                        @for($month = 1 ; $month <= 12 ; $month++)
                                            <option value="{{$month}}" {{ ($month== date("n")) ? ' selected="true"' :
                                            ''}}>{{date('F', mktime(0, 0, 0, $month, 1, 2017))}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div id="examcontainer" style="display: none;">
                                <div class="row">
                                    <table id="examcontainertable" class="table table-striped span12">
                                        <thead>
                                        <th>Exam Name</th>
                                        <th>Times Taken</th>
                                        <th>Passed</th>
                                        <th>Failed</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="trends">
                            Division membership trends, data collection phase still in progress.
                        </div>
                        <div role="tabpanel" class="tab-pane" id="exports">
                            <p>Exports of the statistics information in CSV format.</p>
                            <a href="/mgt/stats/export/overview">Overview</a><br>
                            <a href="/mgt/stats/export/details">Detailed</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/js/jquery.canvasjs.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        const updateExams = () => {
          if ($('#examfacilityselect').val() == 0) {
            return false
          }
          waitingDialog.show()
          let querystring = ''
          if ($('#examYearSelect').val() != 'all') {
            querystring = `?year=${$('#examYearSelect').val()}`
          }
          if ($('#examMonthSelect').val() != 'all') {
            querystring = querystring + `&month=${$('#examMonthSelect').val()}`
          }
          $.ajax({
            url     : $.apiUrl() + '/v2/stats/exams/' + $('#examfacilityselect').val() + querystring,
            method  : 'GET',
            dataType: 'JSON'
          }).done((data) => {
            $('#examcontainertable tbody').html('')
            waitingDialog.hide()
            $('#examcontainer').show()
            for (let i in data.data) {
              if (data.data.hasOwnProperty(i)) {
                if (data.data[i].taken > 0) {
                  $('#examcontainertable tbody').append(`
                  <tr>
                  <td>${data.data[i].name}</td>
                  <td>${data.data[i].taken}</td>
                  <td>${data.data[i].passed} (${Math.floor((data.data[i].passed / data.data[i].taken) * 100)}%)</td>
                  <td>${data.data[i].failed} (${Math.floor((data.data[i].failed / data.data[i].taken) * 100)}%)</td>
                  </tr>
                `)
                }
              }
            }
          })
        }
        $('#examfacilityselect').change(() => {
          updateExams()
        })
        $('#examYearSelect').change(() => {
          if ($('#examYearSelect').val() == 'all') {
            $('#examMonthSelect').val('all')
            return false
          }

          updateExams()
        })
        $('#examMonthSelect').change(() => {
          updateExams()
        })
        $('#detailfacilityselect').change(function () {
          if ($('#detailfacilityselect').val() == '0') return
          $('#detailprocessing').show()
          $.ajax({
            url     : '/mgt/stats/details/' + $('#detailfacilityselect').val(),
            type    : 'GET',
            dataType: 'JSON',
          }).success(function (data) {
            $('#detailprocessing').hide()
            if ($('#detailfacilityselect').val() != 'overview') {
              $('#detailcontainer').show()
              $('#detailcontainertable').show()
              $('#detailtotal').html(data.total)
              $('#detailobs').html(data.OBS)
              $('#detailobsg30').html(data.OBSg30)
              $('#details1').html(data.S1)
              $('#details2').html(data.S2)
              $('#details3').html(data.S3)
              $('#detailc1').html(data.C1)
              $('#detaili1').html(data.I1)
              $('#chartContainer').height(300)
              var chart = new CanvasJS.Chart('chartContainer', {
                legend: {
                  verticalAlign  : 'center',
                  horizontalAlign: 'left'
                },
                theme : 'theme4',
                data  : [
                  {
                    type          : 'pie',
                    indexLabel    : '{label}  {y}',
                    showInLegend  : true,
                    toolTipContent: '{legendText} {y}',
                    startAngle    : -90,
                    dataPoints    : [
                      {y: data.OBS, legendText: 'Observer', label: 'OBS'},
                      {y: data.OBSg30, legendText: 'Observer >30 days', label: 'OBS >30'},
                      {y: data.S1, legendText: 'Student 1', label: 'S1'},
                      {y: data.S2, legendText: 'Student 2', label: 'S2'},
                      {y: data.S3, legendText: 'Student 3', label: 'S3'},
                      {y: data.C1, legendText: 'Controller/Senior Controller', label: 'C1-C3'},
                      {y: data.I1, legendText: 'Instructor and above', label: 'I1+'},
                    ]
                  }
                ]
              })
              chart.render()
            } else {
              $('#detailcontainer').show()
              $('#detailcontainertable').hide()
              //$data.OBS.each()
              $('#chartContainer').height(600)
              var chart = new CanvasJS.Chart('chartContainer', {
                theme: 'theme4',
                axisY: {
                  title        : 'Number of controllers',
                  interval     : 10,
                  intervalType : 'number',
                  titleFontSize: '16',
                  labelFontSize: '16'
                },
                axisX: {
                  interval     : 1,
                  intervalType : 'number',
                  labelFontSize: '16',
                },
                data : [
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'OBS',
                    showInLegend  : true,
                    dataPoints    : data.OBS
                  },
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'S1',
                    showInLegend  : true,
                    dataPoints    : data.S1
                  },
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'S2',
                    showInLegend  : true,
                    dataPoints    : data.S2
                  },
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'S3',
                    showInLegend  : true,
                    dataPoints    : data.S3
                  },
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'C1-C3',
                    showInLegend  : true,
                    dataPoints    : data.C1
                  },
                  {
                    type          : 'stackedColumn',
                    toolTipContent: '{label}<br><span style=\'"\'color: {color};\'"\'><strong>{name}</strong></span>: {y}',
                    name          : 'I1+',
                    showInLegend  : true,
                    dataPoints    : data.I1
                  },
                ]
              })
              chart.render()
            }
          })
        })
      })
    </script>
@endsection
