@extends('layout')
@section('title', 'Policies')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Policies and Documents
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($categories as $category)
                            <li role="presentation" @if(!$category->order) class="active" @endif><a
                                    href="#policy-cat-{{ $category->id }}"
                                    aria-controls="policy-cat-{{ $category->id }}" role="tab"
                                    data-toggle="tab">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                    @foreach($categories as $category)
                        <!-- TOOD: Permissions -->
                            <div role="tabpanel" class="tab-pane active" id="policy-cat-{{ $category->id }}">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Policy</th>
                                        <th>Date</th>
                                        <th>View</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($category->policies as $policy)
                                        <tr>
                                            <td>{{ $policy->ident }} &mdash; {{ $policy->name }}</td>
                                            <td>{{ $policy->effective_date->format("m/d/Y") }}</td>
                                            <td><a href="/docs/{{ $policy->slug }}" target="_blank">
                                                    <button class="btn btn-primary"><i class="fas fa-eye"></i> View
                                                    </button>
                                                </a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
