@extends('layout')
@section('title', 'Controller Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <div class="row">
                        <div class="col-md-8" style="font-size: 16pt;">&nbsp;</div>
                        <form class="form-inline" id="controllerForm">
                            <div class="col-md-4 text-right form-group">
                                <input type="text" id="cidsearch" class="form-control" placeholder="CID">
                                <button type="button" class="btn btn-primary" id="cidsearchbtn"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </h3>
            </div>
        </div>
    </div>
    <script>
        function viewXfer(id) {
            $.post( "{{secure_url('mgt/ajax/transfer/reason')}}", { id: id }, function( data ) {
                bootbox.alert(data);
            });
        }
        $('#controllerForm').submit(function() {
            $('#cidsearchbtn').trigger("click");
            return false;
        })
        $('#cidsearchbtn').click(function() {
            var cid = $('#cidsearch').val();
            cid = cid.replace(/\s+/g,'');
            $('#cidsearch').val(cid);

            if (isNaN($('#cidsearch').val()) && $('#cidsearch').val() != "Katniss")
            {
                bootbox.alert("CID must be numbers only");
                return;
            }
            window.location = "/mgt/controller/" + cid;
        });
    </script>

@stop