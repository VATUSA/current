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
                {!! Form::label('CID') !!}
                {!! Form::text('cid', '', ['class' => 'form-control col-md-5']) !!}
            </div>
            <br><br>
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
      $(document).ready(function () {
        $('[name=cid]').autocomplete({
          source   : '/ajax/cid',
          minLength: 2,
          select   : function (event, ui) {
            $('[name=cid]').val(ui.item.value)

            return false
          }
        })
      })
    </script>
@endsection
