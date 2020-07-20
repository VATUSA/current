@extends('layout')
@section('title', 'CBT Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    @if(\App\Classes\RoleHelper::isVATUSAStaff())
                        <select id="fac" class="mgt-sel">
                            @foreach(\App\Facility::where('active', 1)->orWhere('id','ZAE')->orderBy('name')->get() as $f)
                                <option name="{{$f->id}}" @if($f->id == $fac) selected="true" @endif>{{$f->id}}</option>
                            @endforeach
                        </select>&nbsp;-&nbsp;
                    @endif
                    <a href="/cbt/editor/{{$fac}}">{{$facname}} CBT Editor</a> <i class="fa fa-angle-double-right"></i> {{$blockname}}
                </h3>
            </div>
            <div class="panel-body">
                <ul id="cbtblocksortable" class="sortable">
                    @foreach($chapters as $c)
                        <li id="cbt_{{$c->id}}" name="{{$c->id}}">
                            <i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteChapter({{$c->id}})"></i>&nbsp;&nbsp;
                            <i class="fa @if($c->visible == 1) fa-toggle-on @else fa-toggle-off @endif text-success" id="toggle{{$c->id}}" onClick="toggleChapter({{$c->id}})"></i>
                            <i class="fas fa-pencil-alt text-info" onClick="renameChapter({{$c->id}})"></i>
                            <i class="fas fa-external-link-alt" onClick="changeLink({{$c->id}})"></i>
                            <span id="name_{{$c->id}}">{{$c->name}}</span></li>
                    @endforeach
                </ul>

                <hr>

                <button class="btn btn-success" onClick="newChapter({{$blockid}})">New Chapter</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var chapurls = {
            @foreach ($chapters as $c)
            "ch{{$c->id}}":"{{$c->url}}",
            @endforeach
        };
        $(document).ready(function() {
            $('#fac').change(function() {
                window.location = "/cbt/editor/" + $('#fac').val();
            });
            $('#cbtblocksortable').sortable({
                update: function(event, ui) {
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        async: false, // Prevent quick sorts from processing out of order.
                        data: data,
                        type: 'POST',
                        url: '/cbt/editor/ajax/chapter/' + $('#cbtblocksortable li:first').attr('name')
                    });
                }
            });
            $('#cbtblocksortable').disableSelection();
        });

        function toggleChapter(id) {
            $.ajax({
                type: 'POST',
                url: '/cbt/editor/ajax/chapter/' + id,
                data: { toggle: true },
                success: function(data) {
                    if (data == 1)
                        $('#toggle' + id).attr('class', 'fa fa-toggle-on text-success');
                    else if (data == 0)
                        $('#toggle' + id).attr('class', 'fa fa-toggle-off text-danger');
                    else
                        alert("Unknown data return " + data);
                }
            });
        }

        function deleteChapter(id) {
            bootbox.confirm("Confirm deletion of chapter '" + $('#name_' + id).html() + "'?", function (result) {
                if (result) {
                    $.ajax({
                        url: '/cbt/editor/ajax/chapter/' + id,
                        type: 'DELETE',
                        success: function() {
                            $('#cbt_' + id).remove();
                        }
                    });
                }
            });
        }

        function newChapter(blockid) {
            bootbox.prompt({
                title: "Name for new chapter:",
                value: 'New Training Chapter',
                callback: function(result) {
                    if (result === null) return;

                    $.ajax({
                        url: '/cbt/editor/ajax/chapter/' + blockid,
                        type: 'PUT',
                        data: { name: result },
                        success:function(data) {

                            $("#cbtblocksortable").append('<li id="cbt_' + data + '"><i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteChapter(' + data + ')"></i>&nbsp;&nbsp;<i class="fa fa-toggle-on text-success" id="toggle' + data + '" onClick="toggleChapter(' + data + ')"></i> <i class="fas fa-pencil-alt text-info" onClick="renameChapter(' + data + ')"></i> <i class="fas fa-external-link-alt" onClick="changeLink(' + data + ')"></i> <span id="name_' + data + '">' + result + '</span></li>');
                        }
                    });
                }
            });
        }

        function renameChapter(id) {
            bootbox.prompt({
                title: "New name for chapter \"" + $('#name_'+id).html() + "\"?",
                value: $('#name_' + id).html(),
                callback: function(result) {
                    if (result === null) return;
                    $.ajax({
                        url: '/cbt/editor/ajax/chapter/' + id,
                        data: {name: result},
                        type: 'POST',
                        success: function (data) {
                            if (data == "1") {
                                $('#name_' + id).html(result);
                            } else {
                                alert("Error changing name of block");
                            }
                        }
                    });
                }
            });
        }

        function changeLink(id) {
            bootbox.prompt({
                title: "New link for chapter \"" + $('#name_'+id).html() + "\"?",
                value: eval("chapurls.ch" + id),
                callback: function(result) {
                    if (result === null) return;
                    $.ajax({
                        url: '/cbt/editor/ajax/chapter/' + id,
                        data: {link: result},
                        type: 'POST'
                    });
                }
            });
        }
    </script>
@stop