@extends('layout')
@section('title', 'KB Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="/help/kbe">Knowledgebase Editor</a> <i class="fa fa-angle-double-right"></i> {{$category->name}}
                </h3>
            </div>
            <div class="panel-body">
                <ul id="cbtblocksortable" class="sortable">
                    @foreach($category->questions as $c)
                        <li id="kbe_{{$c->id}}" name="{{$c->id}}">
                            <i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteQuestion({{$c->id}})"></i>&nbsp;&nbsp;
                            <a href="/help/kbe/{{$category->id}}/{{$c->id}}"><i class="fas fa-pencil-alt text-info"></i></a>
                            <span id="name_{{$c->id}}">{{$c->question}}</span></li>
                    @endforeach
                </ul>
                <button class="btn btn-success" onClick="window.location='/help/kbe/{{$category->id}}/0';">New Question</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#cbtblocksortable').sortable({
                update: function(event, ui) {
                    waitingDialog.show("Saving", {dialogSize: "sm", progressType: "ogblue"});
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        async: false, // Prevent quick sorts from processing out of order.
                        data: data,
                        type: 'POST',
                        url: '/help/kbe/ajax/question/order/' + {{$category->id}},
                    }).done(function() {
                        waitingDialog.hide();
                    });
                }
            });
            $('#cbtblocksortable').disableSelection();
        });

        //waitingDialog.show("Saving", {dialogSize: "sm", progressType: "ogblue"});

        function deleteQuestion(id) {
            bootbox.confirm("Confirm deletion of question '" + $('#name_' + id).html() + "'?", function (result) {
                if (result) {
                    $.ajax({
                        url: '/help/kbe/ajax/question/' + id,
                        type: 'DELETE',
                        success: function() {
                            $('#kbe_' + id).remove();
                        }
                    });
                }
            });
        }

        function newQuestion(categoryID) {
            bootbox.dialog({
                title: "Enter new question.",
                message: '<form id="newQuestionForm">'
                    + '<div class="form-group"><label for="newQuestionQuestion">Question</label>'
                    + '<input type="text" class="form-control" id="newQuestionQuestion" placeholder="How do I do this?">'
                    + '</div><div class="form-group"><label for="newQuestionAnswer">Answer</label>'
                    + '<textarea class="form-control" id="newQuestionAnswer" rows="3"></textarea></div></form>',
                buttons: {
                    confirm: {
                        label: "Add",
                        className: "btn-success",
                        callback: function() {
                            waitingDialog.show("Saving...");
                            var q = $('#newQuestionQuestion').val();
                            var a = $('#newQuestionAnswer').val();
                            $.ajax({
                                url: '/help/kbe/ajax/question/' + categoryID,
                                type: 'PUT',
                                data: { question: q, answer: a },
                                success:function(data) {
                                    $("#cbtblocksortable").append('<li id="kbe_' + data + '" name="' + data + '"><i class="fas fa-arrows-alt-v arrows"></i>'
                                            +'<i class="fas fa-trash-alt text-danger" onClick="deleteQuestion(' + data + ')"></i>' +
                                            ' <i class="fas fa-pencil-alt text-info" onClick="editQuestion(' + data + ')"></i>' +
                                            ' <span id="name_' + data + '">' + q + '</span></li>');
                                    $('#newQuestionQuestion').val("");
                                    $('#newQuestionAnswer').val('');
                                    waitingDialog.hide();
                                }
                            });
                        }
                    },
                    cancel: {
                        label: "Cancel",
                        className: 'btn-danger'
                    }
                }
            });
        }

        function editQuestion(id) {
            waitingDialog.show("Loading", {dialogSize: "sm", progressType: "ogblue"});
            $.ajax({
                url: '/help/kbe/ajax/question/' + id,
                type: 'GET',
                success: function (data) {
                    waitingDialog.hide();
                    var j = JSON.parse(data);

                    bootbox.dialog({
                        title: "Edit question.",
                        message: '<form id="newQuestionForm">'
                        + '<div class="form-group"><label for="newQuestionQuestion">Question</label>'
                        + '<input type="text" class="form-control" id="newQuestionQuestion" placeholder="How do I do this?" value="' + j.question + '">'
                        + '</div><div class="form-group"><label for="newQuestionAnswer">Answer</label>'
                        + '<textarea class="form-control" id="newQuestionAnswer" rows="3">' + j.answer + '</textarea></div></form>',
                        buttons: {
                            confirm: {
                                label: "Save",
                                className: "btn-success",
                                callback: function () {
                                    var q = $('#newQuestionQuestion').val();
                                    var a = $('#newQuestionAnswer').val();
                                    waitingDialog.show("Saving...");
                                    $.ajax({
                                        url: '/help/kbe/ajax/question/' + id,
                                        type: 'POST',
                                        data: {
                                            question: q,
                                            answer: a
                                        },
                                        success: function (data) {
                                            waitingDialog.hide();
                                            $('#name_' + id).html(q);
                                        }
                                    });
                                }
                            },
                            cancel: {
                                label: "Cancel",
                                className: 'btn-danger'
                            }
                        },

                    });
                }
            });
        }
    </script>
@stop