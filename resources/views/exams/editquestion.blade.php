@extends('exams.layout')

@section('examcontent')
    @if (isset($success))
    <div class="alert alert-success">
        Question successfully saved.  <a href="/exam/edit/{{$exam->id}}">Return to exam</a>
    </div>
    @endif
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a href="/exam/edit">Exam Center</a> <i class="fa fa-angle-double-right"></i> <a href="/exam/edit/{{$exam->id}}">({{$exam->facility_id}}) {{$exam->name}}</a>
            </h3>
        </div>
        <div class="panel-body">
            {!! Form::open(array('url' => URL::to('/exam/edit/' . $exam->id . '/' . $question->id, [], true), 'files' => true)) !!}
            <div class="form-group">
                {!! Form::label('ID') !!}
                {!! Form::text('id', $question->id, array('readonly' => "true", 'class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Question') !!}
                {!! Form::textarea('question', $question->question, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label("Notes") !!}
                {!! Form::text('notes', $question->notes, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('Type') !!}
                {!! Form::select("qtype", ['Multiple Choice', 'True/False'], $question->type) !!}
            </div>
            <div id="mcbox">
                <div class="form-group">
                    {!! Form::label("Answer") !!}
                    {!! Form::text('correct', $question->answer) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Distractor 1") !!}
                    {!! Form::text('distractor1', $question->alt1) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Distractor 2") !!}
                    {!! Form::text('distractor2', $question->alt2) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Distractor 3") !!}
                    {!! Form::text('distractor3', $question->alt3) !!}
                </div>
            </div>
            <div id="tfbox">
                <div class="form-group">
                    {!! Form::label("Answer") !!}
                    {!! Form::select('tfanswer', ['True' => 'True','False' => 'False'], (($question->type == 1) ? $question->answer : null)) !!}
                </div>
            </div>
            <input type="submit" class="btn btn-success" value="Save">
            {!! Form::close() !!}
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('[name=qtype]').change(function() {
                if ($('[name=qtype]').val() == "1") {
                    $('#mcbox').hide();
                    $('#tfbox').show();
                } else {
                    $('#mcbox').show();
                    $('#tfbox').hide();
                }
            });

            if ($('[name=qtype]').val() == "1") {
                $('#mcbox').hide();
                $('#tfbox').show();
            } else {
                $('#mcbox').show();
                $('#tfbox').hide();
            }
        });
    </script>
@endsection