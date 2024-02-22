@extends('layout')
@section('title', 'Roles Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Roles Management
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped">
                            <tr>
                                <th>CID</th>
                                <th>Name</th>
                                <th>Facility</th>
                                <th>Role</th>
                            </tr>
                            @foreach ($roles as $role)
                                <tr>
                                    <td><a href="/mgt/controller/{{ $role->cid }}">{{ $role->cid }}</a></td>
                                    <td>{{ $role->user()->first()->fullname() }}</td>
                                    <td>{{ $role->facility }}</td>
                                    <td>{{ $role->role }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop