@extends('layout')
@section('title', 'Facility Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <select id="facmgt" class="mgt-sel">@foreach(\App\Models\Facility::where('active', 1)->orderby('id', 'ASC')->get() as $f) <option name="{{$f->id}}">{{$f->id}}</option> @endforeach</select> - Winterfell Facility Management
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#dash" aria-controls="dash" role="tab" data-toggle="tab">Dashboard</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="dash">
                            <table class="fac-dash">
                                <tr>
                                    <td style="width: 33%"><h1><span data-toggle="tooltip" data-placement="bottom" title="Total Controllers"><i class="fa fa-user"></i> 17</span></h1></td>
                                    <td style="width: 34%"><h1> <span data-toggle="tooltip" data-placement="bottom" title="Pending Transfers"><i class="fa fa-user-plus"></i> 0</span></h1></td>
                                    <td><h1> <span data-toggle="tooltip" data-placement="bottom" title="Promotions this Month"><i class="fa fa-star"></i> 1</span></h1></td>
                                </tr>
                            </table>
                            <hr>
                            <h4>Kingdom Administration</h4>
                            <div id="staff-table">
                                <table class="table table-hover">
                                    <thead>
                                    <tr><th>Position</th><th>Name</th></tr>
                                    </thead>
                                    <tbody>
                                    <tr><td>King of the North</td>
                                    <td>Deceased</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <br><iframe width="420" height="315" src="https://www.youtube.com/embed/SZrDEUldGr0?autoplay=1" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#facmgt").change(function() {
                window.location = "{{secure_url("/mgt/facility")}}/" + $("#facmgt").val();
            });
        });
    </script>
@stop