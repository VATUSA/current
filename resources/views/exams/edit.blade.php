@extends('exams.layout')

@section('examcontent')
                Select an exam to edit:
                <div class="form-group">
                    <div class="col-sm-10">
                        <select id="exam" class="form-control">
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ ($exam->facility_id != "0") ? "(" . $exam->facility_id . ") " : "" }} {{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2"><button class="btn btn-primary" onClick="handleEdit()">Edit</button></div>
                    <br><br>
                    <a href="/exam/create" class="btn btn-success">Create new exam</a>
                </div>

    <script type="text/javascript">
        function handleEdit() {
            window.location = "/exam/edit/" + $('#exam option:selected').val();
        }
    </script>
@endsection