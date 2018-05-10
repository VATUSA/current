@extends('exams.layout')

@section('examcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title" id="examheader">Loading exam....</h3>
        </div>
        <div class="panel-body" id="exambody">Loading exam... please wait...</div>
    </div>
    <script type="text/javascript">
        var answer = [];
        var currentQuestion = 0;
        var json = JSON.parse('{!! $json !!}');
        $(document).ready(function() {
            $('#examheader').html("(" + json["facility"] + ") " + json["name"]);
            $('#exambody').html("You are about to take the <strong>" + json["name"] + "</strong> assigned by " + json["facilityName"] + ".<br><br>Keep in mind that all exams are open book, though you are welcome to test yourself by not referencing any material.  Once you answer a question, you cannot go back to it.<br><br>Good luck!<br><br><button class=\"btn btn-info\" id=\"startExam\">Start Exam</button>");
        });
        $('body').on('click', '#startExam', function() {
            generateQuestion();
        });
        $('body').on('click', '#nextQuestion', function() {
            if ($('#answer:checked').length == 0) {
                bootbox.alert("You must answer the question before continuing");
                return;
            }
            //bootbox.alert("Question answer set to " + $('#answer:checked').val());
            var answerObj = { "id": $('#questionid').val(), "answer": $('#answer:checked').val() };
            answer.push(answerObj);
            if (currentQuestion + 1 == json["count"]) {
                $('#exambody').html("Transmitting answers and grading exam... please wait.");
                answerJson = JSON.stringify(answer);
                $.ajax({
                    url: "/exam/" + json['id'],
                    method: "PUT",
                    data: {answer: answerJson}
                })
                .success(function(data) {
                    $('#exambody').html(data);
                })
                .error(function(data) {
                    $('#exambody').html(data);
                });
            } else {
                currentQuestion += 1;
                generateQuestion();
            }
        });

        function generateQuestion() {
            var question = json["questions"][currentQuestion];
            var view = '<input type="hidden" id="questionid" value="' + question["id"] + '">' +
                    '<strong>' + (currentQuestion + 1) + ' of ' + json["count"] + '.</strong> ' +
                    question["question"] + '<br><br>';
            if (question["type"] == 0) {
                var r = randomIntFromInterval(1,4);
                if (r == 1) {
                    var formdata = '<div class="radio"><label><input type="radio" name="answer" id="answer" value="1"> ' + question["one"] + '</label></div>' +
                            '<div class="radio"><label><input type="radio" name="answer" id="answer" value="2"> ' + question["two"] + '</label></div>' +
                            ((question["three"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="3"> ' + question["three"] + '</label></div>' : "") +
                            ((question["four"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="4"> ' + question["four"] + '</label></div>' : "");
                }
                if (r == 2) {
                    var formdata = '<div class="radio"><label><input type="radio" name="answer" id="answer" value="2"> ' + question["two"] + '</label></div>' +
                            '<div class="radio"><label><input type="radio" name="answer" id="answer" value="1"> ' + question["one"] + '</label></div>' +
                            ((question["three"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="3"> ' + question["three"] + '</label></div>' : "") +
                            ((question["four"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="4"> ' + question["four"] + '</label></div>' : "");
                }
                if (r == 3) {
                    var formdata = '<div class="radio"><label><input type="radio" name="answer" id="answer" value="2"> ' + question["two"] + '</label></div>' +
                            ((question["three"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="3"> ' + question["three"] + '</label></div>' : "") +
                            '<div class="radio"><label><input type="radio" name="answer" id="answer" value="1"> ' + question["one"] + '</label></div>' +
                            ((question["four"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="4"> ' + question["four"] + '</label></div>' : "");
                }
                if (r == 4) {
                    var formdata = '<div class="radio"><label><input type="radio" name="answer" id="answer" value="2"> ' + question["two"] + '</label></div>' +
                            ((question["three"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="3"> ' + question["three"] + '</label></div>' : "") +
                            ((question["four"] != null) ? '<div class="radio"><label><input type="radio" name="answer" id="answer" value="4"> ' + question["four"] + '</label></div>' : "") +
                            '<div class="radio"><label><input type="radio" name="answer" id="answer" value="1"> ' + question["one"] + '</label></div>';
                }
            } else {
                var formdata = '<div class="radio"><label><input type="radio" name="answer" id="answer" value="True"> True</label></div>' +
                        '<div class="radio"><label><input type="radio" name="answer" id="answer" value="False"> False</label></div>';
            }
            view = view + formdata +
                    '<br><button id="nextQuestion" class="btn btn-info">Continue</button>';
            $('#exambody').html(view);
        }

        function randomIntFromInterval(min,max)
        {
            return Math.floor(Math.random() * (max-min + 1)+min);
        }
    </script>
@endsection