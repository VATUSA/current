@extends('layout')
@section('title', 'TMU Map Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                @if(\App\Classes\RoleHelper::isVATUSAStaff())
                    <select id="fac" class="mgt-sel">
                        @foreach(\App\Facility::where('active', 1)->orderBy('name')->get() as $f)
                            <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                        @endforeach
                    </select>&nbsp;-&nbsp;
                @endif
                    {{$facname}} TMU Map Management
                </h3>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active"><a href="#facilities" aria-controls="facilities" role="tab" data-toggle="tab">Facilities</a></li>
                    <li role="presentation"><a href="#mapping" aria-controls="mapping" role="tab" data-toggle="tab">Mapping</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="facilities">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Facilities
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p>To create a sub-facility (ie, center area, terminal area) please email vatusa6@vatusa.net with the identifier desired
                                    (up to 4 letters) and English name.  Identifiers to be related to the position, IE, TRACONs like M98, P31, etc. should use an identifier
                                    based on that.  For center areas, use the IATA identifier with a letter or number area designator affixed.  IE, area "A" of ZSE could be
                                    ZSEA.
                                </p>
                                <table class="table table-striped">
                                    @foreach($facilities as $facility)
                                        <tr>
                                            <td><b>{{$facility->id}}</b> - {{$facility->name}}</td>
                                            <td style="text-align: right"><button class="btn btn-success btnColors" data-facility="{{$facility->id}}">Colors</button> <button class="btn btn-primary btnCoords" data-facility="{{$facility->id}}">Boundary</button></td>
                                        </tr>
                                        <tr style="display: none;" id="coords_{{$facility->id}}">
                                            <td colspan="2">
                                                <p class="alert alert-warning">Facility coordinates are entered in JSON array format (<a href="https://www.javatpoint.com/json-array">example</a>) of arrays containing floats representing latitude and longitude
                                                    in decimal degrees. The format is very specific and must be entered correctly or it will not display correctly.  When making changes, it is recommended to check your JSON against
                                                    <a href="https://jsonlint.com/">JSONLint</a> to ensure it is valid JSON.  <b>The values must be float or decimal format, and cannot be quoted.</b>
                                                    Formatting is optional, but proper care to ensure closures of brackets [] and commas after all but the last array are required.
                                                    <b>The last point must be the same as the first point to close the polygon.</b></p>
                                                <form id="boundaryForm" method="post" action="/mgt/tmu/{{$facility->id}}/coords">
                                                    <textarea class="form-control" name="coords" rows="10" id="coordbox_{{$facility->id}}">{{$facility->coords}}</textarea><br>
                                                </form>
                                                <button class="btn btn-primary btnSave" data-facility="{{$facility->id}}">Save</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="mapping">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Mapping
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p>Define lines and markets to be displayed on TMU maps for the parent facility.  Mapping data may be shared between maps.</p>
                                <p>Coming soon.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            var hash = document.location.hash;
            if (hash)
                $('.nav-tabs a[href=' + hash + ']').tab('show');

            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });

            $('#fac').change(function() {
                window.location = "/mgt/tmu/" + $('#fac').val();
            });

            $('.btnCoords').click(function() {
                $('#coords_' + $(this).data('facility')).toggle();
            });
            $('.btnColors').click(function() {
                window.location='/mgt/tmu/' + $(this).data('facility') + '/colors';
            })
            $('.btnSave').click(function() {
                try {
                    var fac = $(this).data("facility");
                    var c = $.parseJSON($('#coordbox_' + fac).val())
                } catch (e) {
                    bootbox.alert("The coordinates entered for " + $(this).data("facility") + " are not in the correct format.  Unable to continue.");
                    return false;
                }
                waitingDialog.show("Saving...")
                /*$.ajax({
                    url: '/mgt/tmu/' + $(this).data("facility") + '/coords',
                    method: "post",
                    data: { coords: $('#coordbox_' + fac).val(), token: "" }
                }).always(function() {
                    waitingDialog.hide();
                }).success(function() {
                    bootbox.alert("Coordinates for " + fac + " saved.");
                }).fail(function() {
                    bootbox.alert("There was an error saving coordinates.  Please try again later.");
                });*/
              $('#boundaryForm').submit();
            });
        });
    </script>
@stop
