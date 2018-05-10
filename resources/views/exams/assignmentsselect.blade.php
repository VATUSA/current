@extends('exams.layout')

@section('examcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">View Exam Assignments</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="facility">Select Facility</label>
                <select id="facility" name="facility" class="form-control">
                    <option value="ZAE">ZAE - VATUSA Academy</option>
                    @foreach($facilities as $facility)
                        <option value="{{$facility->id}}"@if (\Auth::user()->facility == $facility->id) selected="true" @endif>{{$facility->id}} - {{$facility->name}}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary" onClick="goToAssign()">View Assignments</button>
        </div>
    </div>
    <script type="text/javascript">
        function goToAssign() {
            window.location = "/exam/view/" + $('#facility').val();
        }
    </script>
@endsection