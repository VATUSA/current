@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
@endpush
@include('mgt.controller.training.view')

<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">Training Records</h3></div>
    <div class="panel-body">
        <div class="col-md-3" style="border-right: 1px solid #ccc;">
            <form class="form-inline">
                <div class="form-group">
                    <label for="tng-artcc-select">ARTCC:</label>
                    <select class="form-control" id="tng-artcc-select" autocomplete="off">
                        <option value="" @if(!$trainingfac) selected @endif>-- Select One --</option>
                        @foreach($trainingfaclist as $fac)
                            <option value="{{ $fac->facility->id }}"
                                    @if($trainingfac == $fac->facility->id) selected @endif>{{ $fac->facility->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <BR>
            <ul class="nav nav-pills nav-stacked" role="tablist" id="pos-types">
                <li role="presentation" class="active"><a href="#training" data-controls="all" aria-controls="all"
                                                          role="tab"
                                                          data-toggle="pill"><i
                            class="fa fa-list"></i> All Records</a></li>
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
                @php $postypes = ['DEL', 'GND', 'TWR', 'APP', 'CTR']; @endphp
                <div role="tabpanel" class="tab-pane" id="all">All!</div>
                @foreach($postypes as $postype)
                    <div role="tabpanel" class="tab-pane @if($postype == 'GND') active @endif'"
                         id="{{strtolower($postype)}}">
                        @php $records = $trainingRecords->filter(function($record) use ($postype) {
                                                                    return preg_match("/$postype\$/", $record->position); })
                        @endphp
                        <table class="training-records-list table table-striped" id="training-records-{{$postype}}">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Position</th>
                                <th>Instructor</th>
                                <th>Duration</th>
                                <th class="alert-ignore">Score</th>
                                <th class="alert-ignore">Actions</th>
                                <th class="alert-ignore">Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td><span
                                            class="hidden">{{$record->session_date->timestamp}}</span>{{ $record->session_date->format('m/d/Y') }}
                                    </td>
                                    <td>{{ $record->position }}</td>
                                    <td>{{ $record->instructor->fullname() }}</td>
                                    <td>{{ substr($record->duration, 0, 5) }}</td>
                                    <td class="alert-ignore">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span
                                                class="glyphicon @if($i > $record->score) glyphicon-star-empty @else glyphicon-star @endif"></span>
                                            &nbsp;
                                        @endfor
                                    </td>
                                    <td class="alert-ignore">
                                        <div class="btn-group">
                                            <button class="btn btn-primary view-record" data-id="{{ $record->id }}"><span
                                                    class="glyphicon glyphicon-eye-open"></span></button>
                                            @php $canModify = \App\Classes\RoleHelper::isVATUSAStaff() || \App\Classes\RoleHelper::isFacilitySeniorStaff(Auth::user()->cid, $trainingfac) || $record->instructor_id == Auth::user()->cid; @endphp
                                            @if($canModify)
                                                <button class="btn btn-warning"><span
                                                        class="glyphicon glyphicon-pencil"></span></button>
                                                <button class="btn btn-danger delete-tr"
                                                        data-id="{{ $record->id }}"><span
                                                        class="glyphicon glyphicon-remove"></span></button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>@switch($record->location)
                                            @case(0) Classroom @break
                                            @case(1) Live @break
                                            @case(2) Sweatbox @break
                                        @endswitch
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://kit.fontawesome.com/63288b607f.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>
    <script src="{{ secure_asset("js/training.js") }}" type="text/javascript"></script>
@endpush