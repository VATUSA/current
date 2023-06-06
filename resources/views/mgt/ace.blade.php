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
                        <form class="form-inline" action="{{ url('/mgt/ace') }}" style="margin-bottom:5px;"
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

                            <label for="cid">Add Controller:</label> 
                            <input type="text" name="cid" id="cidsearch" class="form-control" placeholder="CID or Last Name">

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

      $('#cidsearch').devbridgeAutocomplete({
          lookup: [],
          onSelect: (suggestion) => {
              $('#cidsearch').val(suggestion.data);
          }
      });
      var prevVal = '';
      $('#cidsearch').on('change keydown keyup paste', function() {
          let newVal = $(this).val();
          if (newVal.length === 4 && newVal !== prevVal) {
              let url = '/v2/user/' + (isNaN(newVal) ? 'filterlname/' : 'filtercid/');
              prevVal = newVal;
              $.get($.apiUrl() + url + newVal)
              .success((data) => {
                  $('#cidsearch').devbridgeAutocomplete().setOptions({
                      lookup: $.map(data.data, (item) => {
                          return { value: item.fname + ' ' + item.lname + ' (' + item.cid + ')', data: item.cid };
                      })
                  });
                  $('#cidsearch').focus();
              });
          }
      });
    </script>
@stop