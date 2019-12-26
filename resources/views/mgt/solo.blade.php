@extends('layout')
@section('title', 'Manage Solo Endorsements')
@push('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td>CID</td>
                        <td>Name</td>
                        <td>Position</td>
                        <td>Valid through</td>
                        <td>Action</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\SoloCert::where('expires','>=',\DB::raw('NOW()'))->get() as $cert)
                        <tr>
                            <td>{{$cert->cid}}</td>
                            <td>{{$cert->user()->first()->fullname()}}</td>
                            <td>{{$cert->position}}</td>
                            <td>{{$cert->expires}}</td>
                            <td>
                                <button type="button" class="btn btn-danger delete-solo" data-id="{{ $cert->id }}"><i
                                        class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    <form action="/mgt/solo" id="add-solo-form">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <tr>
                            <td colspan="2"><input type="text" name="cid" placeholder="CID" class="form-control"
                                                   style="width:110px;"></td>
                            <td><input type="text" name="position" placeholder="Position" class="form-control"
                                       style="width:150px"></td>
                            <td><input type="date" name="expDate" placeholder="Expiration (YYYY-MM-DD)"
                                       class="form-control" style="padding:0; width:150px;"></td>
                            <td>
                                <button class="btn btn-success" id="add-solo"><i class="fa fa-check"></i>
                                    Add
                                </button>
                            </td>
                        </tr>
                    </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
      $(function () {
        $('.delete-solo').click(function () {
          let btn = $(this),
              id  = btn.data('id')
          btn.prop('disabled', true)
          $.ajax({
            method: 'DELETE',
            url   : $.apiUrl() + '/v2/solo/',
            data  : {id: id}
          }).done(function () {
            swal('Success!', 'Solo endorsement deleted.', 'success').then(() => { location.reload(true) })
          }).error(function () {
            swal('Error!', 'Unable to delete solo endorsement. Try again later.', 'error')
            btn.prop('disabled', false)
          })
        })
        $('#add-solo').click(function (e) {
          e.preventDefault()
          let btn  = $(this),
              form = $('#add-solo-form')
          btn.prop('disabled', true)
          $.ajax({method: 'POST', url: $.apiUrl() + '/v2/solo', data: form.serialize()})
            .done(function (result) {
              swal('Success!', 'Solo endorsement has been added.', 'success').then(() => { location.reload() })
            })
            .error(function (result) {
              btn.prop('disabled', false)
              swal('Error!', 'Unable to add solo endorsement. ' + result.responseJSON.msg, 'error')
            })
        })
      })
    </script>
@endsection