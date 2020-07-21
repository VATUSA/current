@extends('layout')
@section('title', 'ACE Team Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    ACE Team Management
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-inline" action="{{ secure_url('/mgt/ace') }}" style="margin-bottom:5px;"
                              method="post">
                            @if(Session::has('aceSubmit'))
                                @if(Session::get('aceSubmit') !== true)
                                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>
                                        <strong>Error!</strong>
                                        There was an error adding the controller to the ACE
                                        team.<strong> {{ Session::get('aceSubmit') }}</strong></div>
                                @else
                                    <div class="alert alert-success"><i class="fa fa-check"></i>
                                        <strong>Success!</strong>
                                        The controller has been added to the ACE team.
                                    </div>
                                @endif
                            @endif
                            <label for="cid">Add Controller:</label> <input type="text" name="cid"
                                                                            class="form-control">
                            <button type="submit" class="btn btn-info">Add</button>
                        </form>
                        <table class="table table-striped">
                            @foreach ($roles as $ace)
                                <tr>
                                    <td width="10%">{{$ace->cid}}</td>
                                    <td width="80%">{{$ace->user()->first()->fullname()}}</td>
                                    <td>
                                        <button class="btn btn-danger" OnClick="aceDelete({{$ace->cid}})">Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
      function aceDelete (cid) {
        bootbox.confirm('Are you sure you want to delete ' + cid + ' from the ACE team?', function (result) {
          if (result === true) window.location = '/mgt/ace/delete/' + cid
        })
      }
    </script>
@stop