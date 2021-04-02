@extends('exams.layout')

@section('examcontent')
    @if (isset($success))
        <div class="alert alert-success">
            <strong>Success!</strong> {{$success}}
        </div>
    @endif
    @if (isset($error))
        <div class="alert alert-danger">
            <strong>Error!</strong> {{$error}}
        </div>
    @endif
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Assign Exam</h3>
        </div>
        <div class="panel-body">
            {!! Form::open(array('url' => URL::to('/exam/assign', [], true))) !!}
            <div class="form-group">
                <label for="cid">CID or Last Name:</label>
                <input type="text" name="cid" class="form-control" id="cidsearch">
            </div>
            <div class="form-group">
                {!! Form::label("Exam") !!}
                <select class="form-control" name="exam">
                    <option value="0">Select Exam</option>
                    @foreach($exams as $fac => $examArr)
                        <optgroup label="{{ $fac }}">
                            @foreach($examArr as $exam)
                                <option value="{{ $exam['id'] }}">{{ $exam['name'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                {!! Form::label("Expire") !!}
                <select class="form-control" name="expire">
                    @foreach ($expireoptions as $e)
                        <option value="{{$e}}">{{$e}}@if ($e > 1) Days @else Day @endif</option>
                    @endforeach
                </select>
            </div>
            <input type="submit" class="btn btn-success" value="Assign">
            {!! Form::close() !!}
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
                        lookup: $.map(data.data, (item) => {
                            return { value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid };
                        })
                    });
                    $('#cidsearch').focus();
                });
            }
      });
    </script>

@endsection
