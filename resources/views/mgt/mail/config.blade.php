@extends('mgt.mail.layout')
@section('mailcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Staff Email Config
            </h3>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" action="{{url("/mgt/mail/conf")}}" method="POST" id="emailchange">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_type" value="{{ $type }}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Email:</label>
                    <div class="col-sm-10">
                        <label class="form-control-static">{{$email}}</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Type:</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><select id="type" class="form-control" name="type">
                                <option value="0"{{($type==0)?" selected":""}}>Full Account</option>
                                <option value="1"{{($type==1)?" selected":""}}>Forward</option>
                            </select></p>
                    </div>
                </div>
                <div id="forwardGroup"{!!($type==0)?" style=\"display: none;\"":""!!}>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Forward To:</label>
                        <div class="col-sm-10">
                            <input type="text" name="dest" id="forwardTo" class="form-control" value="{!! (($dest)?$dest:"") !!}">
                        </div>
                    </div>
                </div>
                <div id="fullGroup"{!! ($type==1)?" style=\"display: none;\"":"" !!}>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password:</label>
                        <div class="col-sm-10">
                            <input type="password" name="password" id="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Strength:</label>
                        <div class="col-sm-10">
                            <span id="passwordStrength">?</span>/100<br>* Must be 65 or higher.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" class="btn btn-success btn-submit" value="Save">
                    </div>
                </div>
            </form>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#emailchange').submit(function() {
            if ($('#type').val() == "0" && $('#passwordStrength').val() < 65) {
                e.preventDefault()
                bootbox.prompt("Password strength must be equal to or above 65");
            }
        })
    });
    $("#password").keyup(function() {
        $.ajax({
            url: "/ajax/passstrength/" + $('#password').val(),
            async: true
        }).success(function(data) {
            $('#passwordStrength').html(data);
        });
    });
    $("#type").change(function() {
        if ($('#type').val() == "1") {
            $('#fullGroup').hide();
            $('#forwardGroup').show();
        } else {
            $('#fullGroup').show();
            $('#forwardGroup').hide();
        }
    });
</script>
@stop