@extends('layout')
@section('title', 'Rating Exam Evaluations')
@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title"><i class="fas fa-check-double"></i> Rating Exam
                    Evaluations @if(!\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                        - {{ Auth::user()->facility()->name }} @endif</h3>
            </div>
            <div class="panel-body">
                <div class="col-md-3" style="border-right: 1px solid #ccc;">
                    @if(\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                        <form class="form-inline" action="{{ url("/mgt/facility/training/evals") }}" method="POST"
                              id="training-artcc-select-form">
                            <div class="form-group">
                                <label for="tng-artcc-select">ARTCC:</label>
                                <select class="form-control" id="tng-artcc-select" autocomplete="off" name="fac">
                                    <option value="" @if(!$trainingfac) selected @endif>-- Select One --</option>
                                        @foreach($facilities as $fac)
                                            <option value="{{ $fac->id }}"
                                                    @if($trainingfac == $fac->id) selected @endif>{{ $fac->name }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </form>
                    @endif
                    <BR>
                    <ul class="nav nav-pills nav-stacked" role="tablist" id="pos-types">
                        <li role="presentation" class="active"><a href="#training" data-controls="all"
                                                                  aria-controls="all"
                                                                  role="tab"
                                                                  data-toggle="pill"><i
                                    class="fa fa-list"></i> All Evaluations</a></li>
                        <hr>
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
                        @php $postypes = ['gnd', 'twr', 'app', 'ctr']; @endphp
                        <div role="tabpanel" class="tab-pane active" id="all">
                            @if(!$trainingfac)
                                <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Please
                                    select a facility.
                                </div>
                            @elseif($evals->count())
                                <table class="training-evals-list table table-striped" id="training-records-all">
                                    <thead>
                                    <tr>
                                        <th>Exam Date</th>
                                        <th>Position</th>
                                        <th>Student</th>
                                        <th>Instructor</th>
                                        <th>Result</th>
                                        <th>C/S/U</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($evals as $eval)
                                        @php $color = $eval->result ? 'success' : 'danger'; @endphp
                                        <tr @if($color) class="{{ $color }}" @endif>
                                            <td><span
                                                    class="hidden">{{$eval->exam_date->timestamp}}</span>{{ $eval->exam_date }}
                                            </td>
                                            <td>{{ $eval->exam_position }}</td>
                                            <td>{!! $eval->student ? $eval->student->fullname() : "<em> Account Erased</em>" !!}</td>
                                            <td>{!! $eval->instructor ? $eval->instructor->fullname() : "<em> Account Erased</em>" !!}</td>
                                            <td>@if($eval->result) <i class="fas fa-check"></i> Pass @else <i
                                                    class="fas fa-times"></i>
                                                Fail @endif</td>
                                            <td><strong
                                                    class="text-info">{{ $eval->results()->where('result', 1)->count() }}</strong>/<strong
                                                    class="text-success">{{ $eval->results()->where('result', 2)->count() }}</strong>/<strong
                                                    class="text-danger">{{ $eval->results()->where('result', 3)->count() }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ url('/mgt/facility/training/eval/' . $eval->id . '/view') }}"
                                                   target="_blank">
                                                    <button class="btn btn-primary"><span
                                                            class="glyphicon glyphicon-eye-open"></span>
                                                        View
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span>
                                    There
                                    are no Rating Exam evaluations.
                                </div>
                            @endif
                            <input type="hidden" id="fac" value="{{ $trainingfac }}">
                        </div>
                        @foreach($postypes as $postype)
                            <div role="tabpanel" class="tab-pane"
                                 id="{{strtolower($postype)}}">
                                @if(!$trainingfac)
                                    <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span>
                                        Please select a facility.
                                    </div>
                                @elseif($evals->count())
                                    @php $records = $evals->filter(function($eval) use ($postype) {
                                                                    return $eval->form->position === $postype;
                                                                    });
                                    @endphp
                                    <table class="training-evals-list table table-striped"
                                           id="training-records-{{$postype}}">
                                        <thead>
                                        <tr>
                                            <th>Exam Date</th>
                                            <th>Position</th>
                                            <th>Student</th>
                                            <th>Instructor</th>
                                            <th>Result</th>
                                            <th>C/S/U</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($records as $eval)
                                            @php $color = $eval->result ? 'success' : 'danger'; @endphp
                                            <tr @if($color) class="{{ $color }}" @endif>
                                                <td><span
                                                        class="hidden">{{$eval->exam_date->timestamp}}</span>{{ $eval->exam_date }}
                                                </td>
                                                <td>{{ $eval->exam_position }}</td>
                                                <td>{!! $eval->student ? $eval->student->fullname() : "<em> Account Erased</em>" !!}</td>
                                                <td>{!! $eval->instructor ? $eval->instructor->fullname() : "<em> Account Erased</em>" !!}</td>
                                                <td>@if($eval->result) <i class="fas fa-check"></i> Pass @else <i
                                                        class="fas fa-times"></i>
                                                    Fail @endif</td>
                                                <td><strong
                                                        class="text-info">{{ $eval->results()->where('result', 1)->count() }}</strong>/<strong
                                                        class="text-success">{{ $eval->results()->where('result', 2)->count() }}</strong>/<strong
                                                        class="text-danger">{{ $eval->results()->where('result', 3)->count() }}</strong>
                                                </td>
                                                <td>
                                                    <a href="{{ url('/mgt/facility/training/eval/' . $eval->id . '/view') }}"
                                                       target="_blank">
                                                        <button class="btn btn-primary"><span
                                                                class="glyphicon glyphicon-eye-open"></span>
                                                            View
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-warning"><span
                                            class="glyphicon glyphicon-warning-sign"></span>
                                        There are no Rating Exam evaluations.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
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