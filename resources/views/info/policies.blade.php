@extends('layout')
@section('title', 'Policies')
@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Policies & Downloads
                </h3>
            </div>
            <div class="panel-body">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        @php $canView = []; @endphp
                        @foreach($categories as $category)
                            @foreach($category->policies as $policy)
                                @if(\App\Classes\RoleHelper::canView($policy))
                                    @php $canView[] = $category->id; @endphp

                                    <li role="presentation" @if(!$category->order) class="active" @endif><a
                                            href="#policy-cat-{{ $category->id }}"
                                            aria-controls="policy-cat-{{ $category->id }}" role="tab"
                                            data-toggle="tab">{{ $category->name }}</a></li>
                                    @break(2)
                                @endif
                            @endforeach
                        @endforeach
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        @foreach($categories as $category)
                            @if(!in_array($category->id, $canView)) @continue @endif
                            <div role="tabpanel" class="tab-pane @if(!$category->order) active @endif"
                                 id="policy-cat-{{ $category->id }}">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Policy</th>
                                        <th>Effective Date</th>
                                        <th>Description</th>
                                        <th>View</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($category->policies as $policy)
                                        @if(!\App\Classes\RoleHelper::canView($policy)) @continue @endif
                                        <tr>
                                            <td>{{ $policy->ident }} &mdash; {{ $policy->title }}</td>
                                            <td>{{ $policy->effective_date->format('m/d/Y') }}
                                                @if($policy->updated_at->format('m/d/Y') !== $policy->effective_date->format('m/d/Y'))
                                                    <br>
                                                    <strong>Modified: </strong>{{ $policy->effective_date->format('m/d/Y') }}@endif
                                            </td>
                                            <td>{{ $policy->description }}</td>
                                            <td><a href="/info/policies/{{ $policy->slug }}" target="_blank">
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
