@extends('exams.layout')

@section('examcontent')
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="/exam/edit">Exam Center</a> <i class="fa fa-angle-double-right"></i> ({{$exam->facility_id}}) {{$exam->name}}
                </h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="examname">Exam Name</label>
                    <div class="input-group">
                    <span class="input-group-btn">
                        <input class="form-control" type="text" name="name" id="examname" value="{{$exam->name}}">
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onClick="saveName({{$exam->id}})" id="savenamebtn">Save Name</button>
                        <button class="btn btn-danger" onClick="deleteExam({{$exam->id}})" id="deletenamebtn">Delete Exam</button>
                        <button class="btn btn-success" onClick="downloadExam({{$exam->id}})" id="downloadbtn">Download Exam</button>
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cbt">CBT Requirement</label>
                    <div class="input-group">
                    <span class="input-group-btn">
                        <select id="cbt" class="form-control">
                            <option value="0">None</option>
                            @foreach($blocks as $block)
                                <option value="{{$block->id}}"@if ($exam->cbt_required == $block->id) selected="true"@endif>{{$block->name}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onClick="saveCBT({{$exam->id}})" id="savecbtbtn">Save CBT Requirement</button>
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="retakeperiod">Auto-reassign Waiting Period</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <select id="retakeperiod" class="form-control">
                                <option value="0"@if($exam->retake_period == 0) selected="true"@endif>No auto-reassign</option>
                                @foreach($retakes as $retake)
                                    <option value="{{$retake}}"@if($retake == $exam->retake_period) selected="true"@endif>{{$retake}}@if($retake < 2) Day @else Days @endif</option>
                                @endforeach
                            </select>
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" onClick="saveRetake({{$exam->id}})" id="saveretakebtn">Save Retake</button>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="numbertake">Number to Ask (0 = all)</label>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <input type="text" id="numbertake" class="form-control" value="{{$exam->number}}">
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" onClick="saveNumber({{$exam->id}})" id="savenumbertake">Save Number</button>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampassing">Passing Score</label>
                    <div class="input-group">
                    <span class="input-group-btn">
                        <input class="form-control" type="text" name="passing" id="exampassing" value="{{$exam->passing_score}}">%
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onClick="savePassing({{$exam->id}})" id="savepassingbtn">Save</button>
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampassing">Answer Visibility on Results</label>
                    <div class="input-group">
                    <span class="input-group-btn">
                        <select id="answervisibility" class="form-control">
                            <option value="user_only"@if($exam->answer_visibility=="user_only") selected="true"@endif>Only display user's answers</option>
                            <option value="all"@if($exam->answer_visibility=="all") selected="true"@endif>Always display user's answers and correct answers</option>
                            <option value="all_passed"@if($exam->answer_visibility=="all_passed") selected="true"@endif>On Pass: Display user's and correct answers, On Fail: Display only user's answers</option>
                        </select>
                    </span>
                        <span class="input-group-btn">
                        <button class="btn btn-primary" onClick="saveVisibility({{$exam->id}})" id="savevisibilitybtn">Save</button>
                    </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="active">Active</label>
                    <div class="input-group">
                    <span class="input-group-btn">
                        <select id="active" class="form-control">
                            <option value="0"{{($exam->is_active==0)?' selected="true"':""}}>No</option>
                            <option value="1"{{($exam->is_active==1)?' selected="true"':""}}>Yes</option>
                        </select>
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-primary" onClick="saveActive({{$exam->id}})" id="saveactive">Save</button>
                    </span>
                    </div>
                </div>
                <!--<div class="form-group"-->
                <table width="100%" class="table">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th>Question</th>
                            <th width="5%">Type</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    @foreach($questions as $q)
                        <tr id="q{{$q->id}}">
                            <td>{{$q->id}}</td>
                            <td>{{$q->question}}</td>
                            <td>@if($q->type == 1) T/F @else MC @endif</td>
                            <td><i class="fas fa-edit" onClick="editQuestion({{$q->id}})"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteQuestion({{$q->id}})"></i></td>
                        </tr>
                    @endforeach
                </table>
                <br>
                <button class="btn btn-success" onClick="newQuestion({{$exam->id}})">New Question</button>
            </div>
        </div>

    <script type="text/javascript">
        function downloadExam(id)
        {
            window.location="/exam/download/" + id;
            return false;
        }
        function deleteQuestion(id)
        {
            $.ajax({
                type: "DELETE",
                url: "/exam/edit/{{$exam->id}}/" + id,
                success: function() {
                    $('#q' + id).remove();
                }
            });
        }
        function deleteExam(exam) {
            bootbox.confirm("Are you sure you wish to delete this exam?  You cannot reverse this action.", function(result) {
                if (result)
                    window.location="/exam/delete/" + exam;
            });
        }
        function newQuestion(exam) {
            window.location = "/exam/edit/" + exam + "/0";
        }
        function editQuestion(id) {
            window.location = "/exam/edit/{{$exam->id}}/" + id;
        }
        function saveName(id)
        {
            $('#savenamebtn').html("Saving...");
            $('#savenamebtn').attr('class','btn btn-warning');
            $.ajax({
                url: "/exam/edit/" + id,
                type: "POST",
                data: { "name" : $('#examname').val() },
                success: function() {
                    $('#savenamebtn').html("Saved");
                    $('#savenamebtn').attr("class", "btn btn-success");
                    setTimeout(function() { $('#savenamebtn').attr("class","btn btn-primary").html("Save Name"); }, 3000);
                },
                error: function() {
                    $('#savenamebtn').html("Error Encountered");
                    $('#savenamebtn').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#savenamebtn').attr("class","btn btn-primary").html("Save Name"); }, 3000);
                }
            });
        }
        function saveActive(id) {
            $('#saveactive').html("Saving...");
            $('#saveactive').attr('class', "btn btn-warning");
            $.ajax({
                type: "POST",
                url: "/exam/edit/" + id,
                data: { "active" : $('#active').val() },
                success: function() {
                    $('#saveactive').html("Saved");
                    $('#saveactive').attr("class", "btn btn-success");
                    setTimeout(function() { $('#saveactive').attr("class","btn btn-primary").html("Save"); }, 3000);
                },
                error: function() {
                    $('#saveactive').html("Error Encountered");
                    $('#saveactive').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#saveactive').attr("class","btn btn-primary").html("Save"); }, 3000);
                }
            });
        }
        function saveVisibility(id) {
            $('#savevisibilitybtn').html("Saving...");
            $('#savevisibilitybtn').attr('class', "btn btn-warning");
            $.ajax({
                type: "POST",
                url: "/exam/edit/" + id,
                data: { "visibility" : $('#answervisibility').val() },
                success: function() {
                    $('#savevisibilitybtn').html("Saved");
                    $('#savevisibilitybtn').attr("class", "btn btn-success");
                    setTimeout(function() { $('#savevisibilitybtn').attr("class","btn btn-primary").html("Save"); }, 3000);
                },
                error: function() {
                    $('#savevisibilitybtn').html("Error Encountered");
                    $('#savevisibilitybtn').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#savevisibilitybtn').attr("class","btn btn-primary").html("Save"); }, 3000);
                }
            });
        }
        function savePassing(id)
        {
            $('#savepassingbtn').html("Saving...");
            $('#savepassingbtn').attr('class','btn btn-warning');
            $.ajax({
                url: "/exam/edit/" + id,
                type: "POST",
                data: { "passing" : $('#exampassing').val() },
                success: function() {
                    $('#savepassingbtn').html("Saved");
                    $('#savepassingbtn').attr("class", "btn btn-success");
                    setTimeout(function() { $('#savepassingbtn').attr("class","btn btn-primary").html("Save"); }, 3000);
                },
                error: function() {
                    $('#savepassingbtn').html("Error Encountered");
                    $('#savepassingbtn').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#savepassingbtn').attr("class","btn btn-primary").html("Save"); }, 3000);
                }
            });
        }
        function saveRetake(id)
        {
            $('#saveretakebtn').html("Saving...");
            $('#saveretakebtn').attr('class','btn btn-warning');
            $.ajax({
                url: "/exam/edit/" + id,
                type: "POST",
                data: { "retake" : $('#retakeperiod').val() },
                success: function() {
                    $('#saveretakebtn').html("Saved");
                    $('#saveretakebtn').attr("class", "btn btn-success");
                    setTimeout(function() { $('#saveretakebtn').attr("class","btn btn-primary").html("Save Retake"); }, 3000);
                },
                error: function() {
                    $('#saveretakebtn').html("Error Encountered");
                    $('#saveretakebtn').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#saveretakebtn').attr("class","btn btn-primary").html("Save Retake"); }, 3000);
                }
            });
        }
        function saveNumber(id) {
            $('#savenumbertake').html("Saving...").attr('class', "btn btn-warning");
            $.ajax({
                type: "POST",
                url: "/exam/edit/" + id,
                data: { "number": $('#numbertake').val() },
                success:function() {
                    $('#savenumbertake').html('Saved').attr('class', 'btn btn-success');
                    setTimeout(function() { $('#savenumbertake').attr('class', 'btn btn-primary').html('Save Number'); }, 3000);
                },
                error: function() {
                    $('#savenumbertake').html('Error Encountered').attr('class', 'btn btn-danger');
                    setTimeout(function() { $('#savenumbertake').attr('class', 'btn btn-primary').html('Save Number'); }, 3000);
                }
            })
        }
        function saveCBT(id) {
            $('#savecbtbtn').html("Saving...");
            $('#savecbtbtn').attr('class', "btn btn-warning");
            $.ajax({
                type: "POST",
                url: "/exam/edit/" + id,
                data: { "cbt" : $('#cbt').val() },
                success: function() {
                    $('#savecbtbtn').html("Saved");
                    $('#savecbtbtn').attr("class", "btn btn-success");
                    setTimeout(function() { $('#savecbtbtn').attr("class","btn btn-primary").html("Save CBT Requirement"); }, 3000);
                },
                error: function() {
                    $('#savecbtbtn').html("Error Encountered");
                    $('#savecbtbtn').attr("class", "btn btn-danger");
                    setTimeout(function() { $('#savecbtbtn').attr("class","btn btn-primary").html("Save CBT Requirement"); }, 3000);
                }
            });
        }
    </script>
@endsection