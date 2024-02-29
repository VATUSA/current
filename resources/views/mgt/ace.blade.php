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
                        <table class="table table-striped">
                            @foreach ($roles as $ace)
                                <tr>
                                    <td width="10%"><a href="/mgt/controller/{{ $ace->cid }}">{{$ace->cid}}</a></td>
                                    <td width="80%">{{$ace->user()->first()->fullname()}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop