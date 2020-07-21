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
                @elseif (\App\Classes\RoleHelper::isAcademyStaff() && \App\Classes\RoleHelper::isFacilitySeniorStaff())
                        <select id="fac" class="mgt-sel">
                            <option name="ZAE"@if($fac == "ZAE") selected="true" @endif>ZAE</option>
                            <option name="{{\Auth::user()->facility}}"@if($fac != "ZAE") selected="true" @endif>{{\Auth::user()->facility}}</option>
                        </select>&nbsp;-&nbsp;
                @endif
                    {{$facname}} CBT Editor
                </h3>
            </div>
            <div class="panel-body">
                <ul id="cbtblocksortable" class="sortable">
                    @foreach($blocks as $b)
                        <li id="cbt_{{$b->id}}">
                            <i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteBlock({{$b->id}})"></i>&nbsp;&nbsp;
                            <i class="fa @if($b->visible == 1) fa-toggle-on @else fa-toggle-off @endif text-success" id="toggle{{$b->id}}" onClick="toggleBlock({{$b->id}})"></i>
                            <i class="fas fa-pencil-alt text-info" onClick="renameBlock({{$b->id}})"></i>
                            <i class="fa fa-lock" onClick="changeAccess({{$b->id}}, '{{$b->level}}')"></i>
                            <a href="/cbt/editor/{{$b->id}}"><span id="name_{{$b->id}}">{{$b->name}}</span></a></li>
                    @endforeach
                </ul>

                <hr>

                <button class="btn btn-success" onClick="newBlock('{{$fac}}')">New Block</button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
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
                        url: '/cbt/editor/ajax/block/order/{{$fac}}'
                    });
                }
            });
            $('#cbtblocksortable').disableSelection();
        });

        function toggleBlock(id) {
            $.post('/cbt/editor/ajax/blocktoggle/' + id, function(data) {
                if (data == 1) { $('#toggle' + id).attr('class', 'fa fa-toggle-on text-success'); }
                else if (data == 0) { $('#toggle' + id).attr('class', 'fa fa-toggle-off text-danger'); }
                else { alert("Unknown data return " + data); }
            });
        }

        function changeAccess(id, curr) {
            bootbox.dialog({
                title: "Select Access Level for Block",
                message: '<form id="newQuestionForm">'
                + '<div class="form-group"><label for="accessLevel">Access Level</label>'
                + '<select name="accessLevel" id="accessLevel" class="form-control">'
                + '<option value="Senior Staff"' + ((curr == "Senior Staff")?' selected="true"':'') + '>Senior Staff</option>'
                + '<option value="Staff"' + ((curr == "Staff")?' selected="true"':'') + '>Staff</option>'
                + '<option value="I1"' + ((curr == "I1")?' selected="true"':'') + '>I1+</option>'
                + '<option value="C1"' + ((curr == "C1")?' selected="true"':'') + '>C1+</option>'
                + '<option value="S1"' + ((curr == "S1")?' selected="true"':'') + '>Students</option>'
                + '<option value="ALL"' + ((curr == "ALL")?' selected="true"':'') + '>All</option>'
                + '</select></div></form>',
                buttons: {
                    confirm: {
                        label: "Save",
                        className: "btn-success",
                        callback: function() {
                            waitingDialog.show("Saving...");
                            var a = $('#accessLevel').val();
                            $.ajax({
                                url: '/cbt/editor/ajax/block/access/' + id,
                                type: 'POST',
                                data: { access: a },
                                success:function(data) {
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

        function deleteBlock(id) {
            bootbox.confirm("Confirm deletion of block?", function (result) {
                if (result) {
                    $.ajax({
                        url: '/cbt/editor/ajax/block/' + id,
                        type: 'DELETE',
                        success: function() {
                            $('#cbt_' + id).remove();
                        }
                    });
                }
            });
        }

        function newBlock(fac) {
            $.ajax({
                url: '/cbt/editor/ajax/block/' + fac,
                type: 'PUT',
                success: function(data) {
                    $('#cbtblocksortable').append('<li id="cbt_' + data + '"><i class="fas fa-arrows-alt-v arrows"></i> <i class="fas fa-trash-alt text-danger" onClick="deleteBlock(' + data + ')"></i>&nbsp;&nbsp;<i class="fa fa-toggle-on text-success" id="toggle' + data + '" onClick="toggleBlock(' + data + ')"></i> <i class="fas fa-pencil-alt text-info" onClick="renameBlock(' + data + ')"></i> <a href="/cbt/editor/' + data + '"><span id="name_' + data + '">New Training Block</span></a></li>');
                }
            });
        }

        function renameBlock(id) {
            bootbox.prompt({
                title: "New name for block \"" + $('#name_'+id).html() + "\"?",
                value: $('#name_' + id).html(),
                callback: function(result) {
                    if (result === null) return;
                    $.ajax({
                        url: '/cbt/editor/ajax/block/rename/' + id,
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
    </script>
@stop