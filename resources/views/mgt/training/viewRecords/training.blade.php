@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush
@include('mgt.training.viewRecords.view')
<div class="col-md-3" style="border-right: 1px solid #ccc;">
    <ul class="nav nav-pills nav-stacked" role="tablist" id="pos-types">
        <li role="presentation" class="active"><a href="#training" data-controls="all" aria-controls="all"
                                                  role="tab"
                                                  data-toggle="pill"><i
                    class="fa fa-list"></i> All Records</a></li>
        <li role="presentation"><a href="#training" data-controls="ots" aria-controls="ots"
                                   role="tab"
                                   data-toggle="pill"><i
                    class="fa fa-flag"></i> OTS Exams</a></li>
        <hr>
        <li role="presentation"><a href="#training" data-controls="del" aria-controls="del" role="tab"
                                   data-toggle="pill">Clearance
                Delivery</a></li>
        <li role="presentation"><a href="#training" data-controls="gnd" aria-controls="gnd" role="tab"
                                   data-toggle="pill">Ground</a></li>
        <li role="presentation"><a href="#training" data-controls="twr" aria-controls="twr" role="tab"
                                   data-toggle="pill">Tower</a></li>
        <li role="presentation"><a href="#training" data-controls="app" aria-controls="app" role="tab"
                                   data-toggle="pill">Approach</a>
        </li>
        <li role="presentation"><a href="#training" data-controls="ctr" aria-controls="ctr" role="tab"
                                   data-toggle="pill">Center</a></li>
    </ul>
</div>
<div class="col-md-9" id="training-content">
    <div class="tab-content">
        <!-- Filters: Major/Minor | Sweatbox/Live | OTS -->
        @php $postypes = ['OTS', 'DEL', 'GND', 'TWR', 'APP', 'CTR']; @endphp
        <div role="tabpanel" class="tab-pane active" id="all">
            @if($trainingRecords->count())
                <table class="fac-training-records-list table table-striped" id="training-records-all">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Position</th>
                        <th>Student</th>
                        <th>Duration</th>
                        <th>Instructor</th>
                        <th class="alert-ignore">Actions</th>
                        <th class="alert-ignore">Month</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($trainingRecords as $record)
                        @php $color = "";
                                    switch($record->ots_status) {
                                        case 1: $color = "success"; break;
                                        case 2: $color = "danger"; break;
                                        case 3: $color = "info"; break;
                                    }
                        @endphp
                        <tr @if($color) class="{{ $color }}" @endif>
                            <td><span
                                    class="hidden">{{$record->session_date->timestamp}}</span>{{ $record->session_date->format('m/d/Y') }}
                            </td>
                            <td>{{ $record->position }}</td>
                            <td>{{ $record->student->fullname() }}</td>
                            <td>{{ substr($record->duration, 0, 5) }}</td>
                            <td>{!! $record->instructor ? $record->instructor->fullname() : "<em> Account Erased</em>" !!} </td>
                            <td class="alert-ignore">
                                <div class="btn-group">
                                    <button class="btn btn-primary view-tr"
                                            data-id="{{ $record->id }}"><span
                                            class="glyphicon glyphicon-eye-open"></span> View
                                    </button>
                                </div>
                            </td>
                            <td>{{ $record->session_date->format('F') }} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span> There are no
                    training records.
                </div>
            @endif
        </div>
        @foreach($postypes as $postype)
            <div role="tabpanel" class="tab-pane"
                 id="{{strtolower($postype)}}">
                @if($trainingRecords->count() )
                    @php $records = $trainingRecords->filter(function($record) use ($postype) {
                                                                    return $postype == "OTS" ? in_array($record->ots_status, [1,2]) : preg_match("/$postype\$/", $record->position);
                                                                    })
                    @endphp
                    <table class="fac-training-records-list table table-striped"
                           id="training-records-{{$postype}}">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Position</th>
                            <th>Student</th>
                            <th>Duration</th>
                            <th>Instructor</th>
                            <th class="alert-ignore">Actions</th>
                            <th class="alert-ignore">Month</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $color = "";
                                    switch($record->ots_status) {
                                        case 1: $color = "success"; break;
                                        case 2: $color = "danger"; break;
                                        case 3: $color = "info"; break;
                                    }
                            @endphp
                            <tr @if($color) class="{{ $color }}" @endif>
                                <td><span
                                        class="hidden">{{$record->session_date->timestamp}}</span>{{ $record->session_date->format('m/d/Y') }}
                                </td>
                                <td>{{ $record->position }}</td>
                                <td>{{ $record->student->fullname() }}</td>
                                <td>{{ substr($record->duration, 0, 5) }}</td>
                                <td>{!! $record->instructor ? $record->instructor->fullname() : "<em>Account Erased</em>" !!}</td>
                                <td class="alert-ignore">
                                    <div class="btn-group">
                                        <button class="btn btn-primary view-tr"
                                                data-id="{{ $record->id }}"><span
                                                class="glyphicon glyphicon-eye-open"></span> View
                                        </button>
                                    </div>
                                </td>
                                <td>{{ $record->session_date->format('F') }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span>
                        There are no training records.
                    </div>
                @endif </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="{{ secure_asset("datetimepicker/datetimepicker.js") }}"></script>
    <script
        src="https://cdn.tiny.cloud/1/zhw7l11edc5qt7r2a27lkrpa8aecclri5bsd4p7vaoet3u00/tinymce/5/tinymce.min.js"></script>
    <script src="https://kit.fontawesome.com/63288b607f.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>
    <script src="{{ secure_asset("js/moment.js") }}" type="text/javascript"></script>
    <script src="{{ secure_asset("js/training.js") }}" type="text/javascript"></script>
@endpush