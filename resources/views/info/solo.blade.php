@extends('layout')
@section('title', 'Solo Endorsements')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <td>CID</td>
                        <td>Name</td>
                        <td>Position</td>
                        <td>Valid through</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\SoloCert::where('expires','>=',\DB::raw('NOW()'))->get() as $cert)
                        <tr>
                            <td>{{$cert->cid}}</td>
                            <td>{{$cert->user()->first()->fullname()}}</td>
                            <td>{{$cert->position}}</td>
                            <td>{{$cert->expires}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection