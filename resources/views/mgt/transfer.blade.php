@extends('layout')
@section('title', 'Facility Transfer')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Facility Transfer
                </h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" action="{{secure_url("/mgt/transfer")}}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">CID or Last Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="cid" value="{{ $cid }}" id="cidsearch">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">New Facility</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="facility">
                                @foreach(\App\Facility::where('active', 1)->orderby('name', 'ASC')->get() as $f)
                                    <option value="{{$f->id}}">{{$f->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Reason</label>
                        <div class="col-sm-offset-2 col-sm-10"><textarea class="form-control" rows="5" name="reason" placeholder="Transfer Reason"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input type="submit" class="btn btn-success" value="Submit" />
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#cidsearch').devbridgeAutocomplete({
            lookup: [],
            onSelect: (suggestion) => {
                $('#cidsearch').val(suggestion.data);
            }
        });

        var prevVal = '';

        $('#cidsearch').on('change keydown keyup paste', function() {
            let newVal = $(this).val();
            if (newVal.length === 4 && newVal !== prevVal) {
                let url = '/v2/user/' + (isNaN(newVal) ? 'filterlname/' : 'filtercid/');
                prevVal = newVal;
                $.get($.apiUrl() + url + newVal)
                .success((data) => {
                    $('#cidsearch').devbridgeAutocomplete().setOptions({
                        lookup: $.map(data, (item) => {
                            return { value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid };
                        })
                    });
                    $('#cidsearch').focus();
                });
            }
        });
    </script>
@stop