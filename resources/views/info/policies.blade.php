@extends('layout')
@section('title', 'Policies & Downloads')
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
                                    @break
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
                                        <th class="hidden-xs">Description</th>
                                        <th>View</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($category->policies as $policy)
                                        @if(!\App\Classes\RoleHelper::canView($policy)) @continue @endif
                                        <tr @if(!$policy->visible) class="danger" @endif>
                                            <td>{{ $policy->ident }} &mdash; {{ $policy->title }}</td>
                                            <td>{{ $policy->effective_date->format('m/d/Y') }}
                                                @if($policy->updated_at->format('m/d/Y') !== $policy->effective_date->format('m/d/Y'))
                                                    <br>
                                                    <strong>Modified: </strong>{{ $policy->updated_at->format('m/d/Y') }}@endif
                                            </td>
                                            <td class="hidden-xs">{{ $policy->description }}</td>
                                            <td><a href="https://vatusa-storage.nyc3.cdn.digitaloceanspaces.com/{{ $policy->slug }}.{{ $policy->extension }}" target="_blank">
                                                    <button class="btn btn-primary" rel="tooltip"
                                                            title="{{ strtoupper($policy->extension) }}"><i
                                                                class="fas fa-eye"></i> View
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
