@extends('layout')
@section('title', 'Policies Management')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Policies and Documents Management
                </h3>
            </div>
            <div class="panel-body">
                <fieldset>
                    <legend>Categories</legend>
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Policies</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->order + 1 }}</td>
                                <td>{{ $category->name + 1 }}</td>
                                <td>{{ $category->policies()->count() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary"><i class="fas fa-arrow-up"></i></button>
                                        <button class="btn btn-primary"><i class="fas fa-arrow-down"></i></button>
                                    </div>
                                    <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3">
                                <button class="btn btn-success" id="newPolicyCategory"><i class="fas fa-plus"></i> Add
                                    New Category
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Policies</legend>
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
                </fieldset>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
      $(document).ready(function () {
        $('#newPolicyCategory').click(function () {
          $(this).attr('disabled', true)
          return window.location = '{{ secure_url('/mgt/policies/newCategory') }}'
        })
      })
    </script>
@endsection