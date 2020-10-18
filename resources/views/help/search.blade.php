@extends('layout')
@section('title', 'Search Tickets')
@section('content')
    <div class="container">
        @if(\App\Classes\RoleHelper::isFacilityStaff() || \App\Classes\RoleHelper::isInstructor() || \App\Classes\RoleHelper::isVATUSAStaff())
            <div class="row">
                <div class="col-lg-12 text-center">
                    <a href="/help/ticket/myassigned" class="btn btn-primary">My Assigned Tickets</a>
                    <a href="/help/ticket/open" class="btn btn-success">Open Tickets</a>
                    <a href="/help/ticket/closed" class="btn btn-info">Closed Tickets</a>
                    <a href="/help/ticket/search" class="btn btn-warning">Search Tickets</a>
                </div>
            </div>

            <hr>
        @endif
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Search Tickets</h3>
            </div>
            <div class="panel-body">
                <form action="/help/ticket/search" method="post" class="form-horizontal">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="searchCID" class="col-sm-2 control-label">Submitting CID</label>
                        <div class="col-sm-10"><input type="text" class="form-control" id="searchCID" name="cid"
                                                      placeholder="CID"></div>
                    </div>
                    <div class="form-group">
                        <label for="searchFacility" class="col-sm-2 control-label">Assigned Facility</label>
                        <div class="col-sm-10">
                            <select id="searchFacility" name="facility" class="form-control">
                                <option value="0">Any</option>
                                <option value="ZHQ">Headquarters</option>
                                <option value="ZAE">Academy</option>
                                @foreach(\App\Facility::where('active', '1')->orderBy('name')->get() as $f)
                                    <option value="{{$f->id}}">{{$f->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="searchStatus" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-2">
                            <select id="searchStatus" name="status" class="form-control">
                                <option value="0">Any</option>
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop