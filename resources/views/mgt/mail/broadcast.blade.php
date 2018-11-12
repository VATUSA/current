@extends('mgt.mail.layout')

@section('scripts')
    <script>
      $('#send-broadcast').click(function () {
        $(this).attr('disabled', true).html('<i class="spinner-icon fa fa-spinner fa-spin"></i>')
      })
    </script>
@endsection

@section('mailcontent')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                @if(empty($cid))
                    Email Broadcast
                @else
                    Send Email
                @endif
            </h3>
        </div>
        <div class="panel-body">
            @if(session('broadcastSuccess'))
                <div class="alert alert-success"><i
                        class="fa fa-check"></i><strong>Success!</strong> {{ session('broadcastSuccess') }}
                </div>
            @elseif(session('broadcastError'))
                <div class="alert alert-danger"><i
                        class="fa fa-warning"></i><strong>Error!</strong> {{ session('broadcastError') }}
                </div>
            @endif
            <form id="broadcast-form" class="form-horizontal" action="{{secure_url("/mgt/mail/broadcast")}}"
                  method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">To:</label>
                    <div class="col-sm-10">
                        @if(empty($cid))
                            <select class="form-control" name="recipients">
                                @if(\App\Classes\RoleHelper::isVATUSAStaff())
                                    <option value="STAFF">All STAFF</option>
                                    <option value="ALL">All VATUSA Controllers</option>
                                    <option value="SRSTAFF">All ATMs DATMs & TAs</option>
                                    <option value="DRCTR">All ATMs and DATMs</option>
                                    <option value="WM">All WMs</option>
                                    <option value="VATUSA">All VATUSA STAFF</option>
                                    <option value="INS">All INSTRUCTIONAL STAFF</option>
                                    <option value="ACE">All ACE Team Members</option>
                                @endif
                                @foreach(\App\Facility::where('active', 1)->orderby('name', 'ASC')->get() as $f)
                                    <option value="{{$f->id}}"
                                            @if ($f->id == \Auth::user()->facility) selected="true" @endif>{{$f->name}}</option>
                                @endforeach
                            </select>
                        @else
                            <p class="form-control-static">{{\App\Classes\Helper::nameFromCID($cid)}} ({{$cid}})</p>
                            <input type="hidden" name="single" value="{{$cid}}">
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">From:</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{Auth::user()->fname}} {{Auth::user()->lname}}
                            ({{Auth::user()->cid}})</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Subject</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="subject" placeholder="Enter subject...">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10"><textarea class="form-control" rows="5" name="email"
                                                                     placeholder="Enter message..."></textarea>
                        <div class="help-block">Use &lt;b&gt;<b>to bold</b>&lt;/b&gt; or &lt;i&gt;<i>to italicise</i>&lt;/i&gt;
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success" id="send-broadcast">Send</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@stop
