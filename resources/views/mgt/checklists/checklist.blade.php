@extends('layout')
@section('title', 'Checklist Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="/mgt/checklists">Checklists</a> <i class="fa fa-angle-double-right"></i> {{$cl->name}} Checklist Editor
                </h3>
            </div>
            <div class="panel-body">
                <ul id="cbtblocksortable" class="sortable">
                    @foreach($cl->items as $c)
                        <li id="cl_{{$c->id}}" name="{{$c->id}}">
                            <i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteItem({{$c->id}})"></i>&nbsp;&nbsp;
                            <i class="fas fa-pencil-alt text-info" onClick="changeItem({{$c->id}})"></i>
                            <span id="name_{{$c->id}}">{{$c->item}}</span></li>
                    @endforeach
                </ul>
                <button class="btn btn-success" onClick="newItem({{$cl->id}})">New Item</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#cbtblocksortable').sortable({
                update: function(event, ui) {
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        async: false, // Prevent quick sorts from processing out of order.
                        data: data,
                        type: 'POST',
                        url: '/mgt/checklists/{{$cl->id}}/order'
                    });
                }
            });
            $('#cbtblocksortable').disableSelection();
        });

        function deleteItem(id) {
            bootbox.confirm("Confirm deletion of checklist item '" + $('#name_' + id).html() + "'?", function (result) {
                if (result) {
                    $.ajax({
                        url: '/mgt/checklists/{{$cl->id}}/' + id,
                        type: 'DELETE',
                        success: function() {
                            $('#cl_' + id).remove();
                        }
                    });
                }
            });
        }

        function newItem(id) {
            bootbox.prompt({
                title: "Item value:",
                value: '',
                callback: function(result) {
                    if (result === null) return;
                    waitingDialog.show("Creating...");
                    $.ajax({
                        url: '/mgt/checklists/' + id,
                        type: 'PUT',
                        data: { name: result },
                        success:function(data) {
                            waitingDialog.hide();
                            $("#cbtblocksortable").append('<li id="cl_' + data + '"><i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteItem(' + data + ')"></i>&nbsp;&nbsp; <i class="fas fa-pencil-alt text-info" onClick="changeItem(' + data + ')"></i> <span id="name_' + data + '">' + result + '</span></li>');
                        }
                    });
                }
            });
        }

        function changeItem(id) {
            bootbox.prompt({
                title: "New entry for item \"" + $('#name_'+id).html() + "\"?",
                value: $('#name_' + id).html(),
                callback: function(result) {
                    if (result === null) return;

                    waitingDialog.show("Saving...");
                    $.ajax({
                        url: '/mgt/checklists/{{$cl->id}}/' + id,
                        data: {name: result},
                        type: 'POST',
                        success: function (data) {
                            waitingDialog.hide();
                            if (data == "1") {
                                $('#name_' + id).html(result);
                            } else {
                                alert("Error changing value of item");
                            }
                        }
                    });
                }
            });
        }
    </script>
@stop