@extends('layout')
@section('title', 'Checklists Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Training Checklists Editor
                </h3>
            </div>
            <div class="panel-body">
                <ul id="cbtblocksortable" class="sortable">
                    @foreach($checklists as $b)
                        <li id="cl_{{$b->id}}">
                            <i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteList({{$b->id}})"></i>&nbsp;&nbsp;
                            <i class="fas fa-pencil-alt text-info" onClick="renameList({{$b->id}})"></i>
                            <a href="/mgt/checklists/{{$b->id}}"><span id="name_{{$b->id}}">{{$b->name}}</span></a></li>
                    @endforeach
                </ul>
                <button class="btn btn-success" onClick="newList()">New Checklist</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#cbtblocksortable').sortable({
                update: function(event, ui) {
                    waitingDialog.show("Saving...");
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        async: false, // Prevent quick sorts from processing out of order.
                        data: data,
                        type: 'POST',
                        url: '/mgt/checklists/order'
                    }).done(function() { waitingDialog.hide(); });
                }
            });
            $('#cbtblocksortable').disableSelection();
        });

        function deleteList(id) {
            bootbox.confirm("Confirm deletion of checklist?", function (result) {
                if (result) {
                    $.ajax({
                        url: '/mgt/checklists/' + id,
                        type: 'DELETE',
                        success: function() {
                            $('#cl_' + id).remove();
                        }
                    });
                }
            });
        }

        function newList() {
            $.ajax({
                url: '/mgt/checklists',
                type: 'PUT',
                success: function(data) {
                    $('#cbtblocksortable').append('<li id="cl_' + data + '"><i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteList(' + data + ')"></i>&nbsp;&nbsp;<i class="fas fa-pencil-alt text-info" onClick="renameList(' + data + ')"></i> <a href="/mgt/checklists/' + data + '"><span id="name_' + data + '">New Training Checklist</span></a></li>');
                }
            });
        }

        function renameList(id) {
            bootbox.prompt({
                title: "New name for checklist \"" + $('#name_'+id).html() + "\"?",
                value: $('#name_' + id).html(),
                callback: function(result) {
                    if (result === null) return;
                    waitingDialog.show("Saving...");
                    $.ajax({
                        url: '/mgt/checklists/' + id,
                        data: {name: result},
                        type: 'POST',
                        success: function (data) {
                            waitingDialog.hide();
                            if (data == "1") {
                                $('#name_' + id).html(result);
                            } else {
                                alert("Error changing name of checklist");
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection