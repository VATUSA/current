@extends('layout')
@section('title', 'TMU Map Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(\App\Helpers\AuthHelper::authACL()->isVATUSAStaff())
                        <select id="fac" class="mgt-sel">
                            @foreach(\App\Models\Facility::where('active', 1)->orderBy('name')->get() as $f)
                                <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                            @endforeach
                        </select>&nbsp;-&nbsp;
                    @endif
                    {{$facname}} TMU Mapping Management
                </h3>
            </div>
            <div class="panel-body">
                <form class='form-horizontal' method="post">
                    <div class='form-group'>
                        <label for='mapname' class='col-sm-2 control-label'>
                            Map Name:
                        </label>
                        <div class='col-sm-10'>
                            <input type="text" name="mapname" class="form-control" value="{{$map->name}}">
                        </div>
                    </div>

                    <div class='form-group'>
                        <label for='facilities' class='col-sm-2 control-label'>
                            Facilities:
                            <br>Prefixing with * enables by default.
                        </label>
                        <div class='col-sm-10'>
                            <textarea name="facilities" class="form-control" rows="3">{{$map->facilities}}</textarea>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label for='mapdata' class='col-sm-2 control-label'>
                            Map Data:
                        </label>
                        <div class='col-sm-10'>
                            <textarea rows="10" class="form-control" name="mapdata" id="mapdata">{{$map->data}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#fac').change(function () {
                window.location = "/mgt/tmu/" + $('#fac').val();
            });
        });
    </script>
@stop
