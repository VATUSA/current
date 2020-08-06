@extends('layout')
@section('title', 'iDENT App Management')

@section('scripts')
    <script>
      $('#send-push').click(function (e) {
        e.preventDefault()
        $(this).attr('disabled', true).html('<i class="spinner-icon fa fa-spinner fa-spin"></i>')
        $(this.form).submit()
      })
    </script>
@endsection
@section('content')
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          @include('mgt.app.subnav')
      </div>

      <div class="col-md-9">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Send Push Notification</h3>
          </div>
        <div class="panel-body">
            @if(session('pushSuccess'))
              <div class="alert alert-success"><i
                class="fa fa-check"></i><strong>Success!</strong> {{ session('pushSuccess') }}
              </div>
            @elseif(session('pushError'))
              <div class="alert alert-danger"><i
                class="fa fa-warning"></i><strong>Error!</strong>{{ session('pushError') }}
              </div>
            @endif
            <form class="form-horizontal" action="{{url("/mgt/app/push")}}" method="POST">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="submitted_by" value="{{Auth::user()->cid}}">
              <div class="form-group">
                <label class="col-sm-2 control-label">Issued by:</label>
                <div class="col-sm-10">
                  <p class="form-control-static">{{Auth::user()->fname}} {{Auth::user()->lname}}
                      ({{Auth::user()->cid}})</p>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Title</label>
                <div class="col-sm-10">
                  <input required class="form-control" name="title">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="message">Message</label>
                <div class="col-sm-10">
                  <textarea required class="form-control" name="message" rows="5"></textarea>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-success" id="send-push">Send</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
