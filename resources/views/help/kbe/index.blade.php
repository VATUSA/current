@extends('layout')
@section('title', 'KB Editor')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Knowledgebase Editor
                </h3>
            </div>
            <table class="table table-hover" id="kbc_table">
                <tbody>
                @foreach(\App\KnowledgebaseCategories::all() as $kbc)
                    <tr>
                        <td id="kb_{{$kbc->id}}"><i class="fas fa-trash-alt text-danger" onClick="deleteKBC({{$kbc->id}})"></i>&nbsp;&nbsp;
                            <i class="fas fa-pencil-alt text-info" onClick="renameKBC({{$kbc->id}})"></i>
                            <a href="/help/kbe/{{$kbc->id}}"><span id="name_{{$kbc->id}}">{{$kbc->name}}</span></a></td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr><td><button class="btn btn-success" onClick="newKBC()">New Category</button></td></tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function deleteKBC(id) {
            bootbox.confirm("Confirm deletion of category?", function (result) {
                if (result) {
                    waitingDialog.show("Processing");
                    $.ajax({
                        url: '/help/kbe/' + id,
                        type: 'DELETE',
                        success: function() {
                            waitingDialog.hide();
                            $('#kb_' + id).remove();
                        }
                    });
                }
            });
        }

        function newKBC() {
            waitingDialog.show("Creating...");
            $.ajax({
                url: '/help/kbe',
                type: 'PUT',
                success: function(data) {
                    waitingDialog.hide();
                    $('#kbc_table tbody').append('<tr><td id="kbc_' + data + '"><i class="fas fa-trash-alt text-danger" onClick="deleteKBC(' + data + ')"></i>&nbsp;&nbsp;<i class="fas fa-pencil-alt text-info" onClick="renameKBC(' + data + ')"></i> <a href="/help/kbe/' + data + '"><span id="name_' + data + '">New Knowledgebase Category</span></a></td></tr>');
                }
            });
        }

        function renameKBC(id) {
            bootbox.prompt({
                title: "New name for category \"" + $('#name_'+id).html() + "\"?",
                value: $('#name_' + id).html(),
                callback: function(result) {
                    waitingDialog.show("Saving");
                    if (result === null) return;
                    $.ajax({
                        url: '/help/kbe/' + id,
                        data: {name: result},
                        type: 'POST',
                        success: function (data) {
                            waitingDialog.hide();
                            $('#name_' + id).html(result);
                        }
                    });
                }
            });
        }
    </script>
@endsection