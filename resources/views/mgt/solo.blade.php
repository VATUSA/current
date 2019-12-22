@extends('layout')
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
                                <button type="button" class="btn btn-danger" onClick="deleteCert({{$cert->id}})"><i
                                            class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    @endforeach
                    <form action="/mgt/solo" method="post">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <tr>
                            <td colspan="2"><input type="text" name="cid" placeholder="CID" class="form-control" style="width:110px;"></td>
                            <td><input type="text" name="position" placeholder="Position" class="form-control" style="width:150px"></td>
                            <td><input type="date" name="expiration" placeholder="Expiration (YYYY-MM-DD)" class="form-control" style="padding:0; width:150px;"></td>
                            <td>
                                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Add</button>
                            </td>
                        </tr>
                    </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function deleteCert(id) {
            $.ajax({
                url: '/mgt/solo/' + id,
                type: 'DELETE'
            }).success(function () {
                location.reload(true);
            });
        }
    </script>
@endsection