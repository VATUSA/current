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
                            <th style="width: 40%">Name</th>
                            <th>Policies</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            <tr id="category-order-{{ $category->order }}" data-cat-id="{{ $category->id }}">
                                <td>{{ $category->order + 1 }}</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Category Name..."
                                               value="{{ $category->name }}" id="category-name-{{ $category->id }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-success save-category" type="button"
                                                    data-id="{{ $category->id }}"><i class="fas fa-check"></i></button>
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $category->policies()->count() }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary move-up @if(!$category->order) hidden @endif"
                                                data-id="{{ $category->id }}"
                                                data-order="{{ $category->order }}"><i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button
                                            class="btn btn-primary move-down @if($category->order + 1 == $categories->count()) hidden @endif"
                                            data-id="{{ $category->id }}"
                                            data-order="{{ $category->order }}"><i class="fas fa-arrow-down"></i>
                                        </button>
                                    </div>
                                    <button class="btn btn-danger delete-category" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"><i class="fas fa-times"></i></button>
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
                                <li role="presentation" @if(!$category->order) class="active"
                                    @endif id="cat-{{ $category->id }}"><a
                                        href="#policy-cat-{{ $category->id }}"
                                        aria-controls="policy-cat-{{ $category->id }}" role="tab"
                                        data-toggle="tab">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                        @foreach($categories as $category)
                            <!-- TOOD: Permissions -->
                                <div role="tabpanel" class="tab-pane @if(!$category->order) active @endif"
                                     id="policy-cat-{{ $category->id }}">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Ident</th>
                                            <th>Title</th>
                                            <th>Dates</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($category->policies as $policy)
                                            <tr>
                                                <td>{{ $policy->order + 1 }}</td>
                                                <td>{{ $policy->ident }}</td>
                                                <td>{{ $policy->title }}</td>
                                                <td>@if($policy->effective_date)
                                                        Effective: {{ $policy->effective_date->format("m/d/Y") }}
                                                        <br>@endif
                                                    Modified: {{ $policy->updated_at->format("m/d/Y") }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button
                                                            class="btn btn-primary policy-move move-up @if(!$policy->order) hidden @endif"
                                                            data-id="{{ $policy->id }}"
                                                            data-order="{{ $policy->order }}"><i
                                                                class="fas fa-arrow-up"></i>
                                                        </button>
                                                        <button
                                                            class="btn btn-primary policy-move move-down @if($policy->order + 1 == $category->policies->count()) hidden @endif"
                                                            data-id="{{ $policy->id }}"
                                                            data-order="{{ $policy->order }}"><i
                                                                class="fas fa-arrow-down"></i>
                                                        </button>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-warning edit-policy"
                                                                data-id="{{ $policy->id }}"
                                                                data-name="{{ $policy->name }}"><i
                                                                class="fas fa-pencil-alt"></i></button>
                                                        <button class="btn btn-danger delete-policy"
                                                                data-id="{{ $policy->id }}"
                                                                data-name="{{ $policy->name }}"><i
                                                                class="fas fa-times"></i></button>
                                                    </div>
                                                    @if($policy->visible)
                                                        <button class="btn btn-primary"><i class="fas fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-default"><i
                                                                class="fas fa-eye-slash"></i></button>
                                                    @endif
                                                </td>
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script
        src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
        integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
        crossorigin="anonymous"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#newPolicyCategory').click(function () {
          $(this).attr('disabled', true)
          return window.location = '{{ secure_url('/mgt/policies/newCategory') }}'
        })
        $('.delete-category').click(function () {
          swal({
            title     : 'Are you sure you want to delete "' + $(this).data('name') + '"?',
            text      : 'This will also delete all policies within the category. This cannot be undone.',
            icon      : 'warning',
            buttons   : true,
            dangerMode: true,
          })
            .then((willDelete) => {
              if (willDelete) {
                window.location = '{{ secure_url("/mgt/policies/deleteCategory/") }}/' + $(this).data('id')
              } else return false
            })
        })
        $('.save-category').click(function () {
          let btn   = $(this),
              id    = btn.data('id'),
              value = $('#category-name-' + id).val()

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')
          $.ajax({
            'url'   : '/mgt/policies/updateCategory/' + id,
            'method': 'PUT',
            'data'  : {'name': value}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to edit category. ' + error, 'error')
            })
            .done(function (result) {
              $('#cat-' + id).find('a').text(value)
              swal('Success!', 'The category has been edited.', 'success')
            })

          btn.attr('disabled', false).html('<i class=\'fas fa-check\'></i>')
        })

        $('.move-up').click(function () {
          let btn     = $(this),
              id      = btn.data('id'),
              order   = parseInt(btn.attr('data-order')),
              tr      = $('tr#category-order-' + order),
              trAbove = $('tr#category-order-' + (order - 1)),
              idAbove = trAbove.data('cat-id')

          if (!order) return null

          tr.stop(true, true)

          console.log('This Order: ' + order)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/updateCategory/' + id,
            'method': 'PUT',
            'data'  : {'order': order - 1}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move category. ' + error, 'error')
            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/updateCategory/' + idAbove,
                'method': 'PUT',
                'data'  : {'order': order}
              })
                .error(function (xhr, status, error) {
                  swal('Error!', 'Unable to move category. ' + error, 'error')
                })
                .done(function (result) {
                  tr.after(trAbove)
                  if (order - 1 == 0) {
                    btn.addClass('hidden')
                  }
                  tr.find('.move-down').removeClass('hidden')
                  trAbove.find('.move-up').removeClass('hidden')
                  if (order + 1 == {{ $categories->count() }})
                    trAbove.find('.move-down').addClass('hidden')
                  else trAbove.find('.move-down').removeClass('hidden')

                  tr.find('td:first-child').text(order)
                  trAbove.find('td:first-child').text(order + 1)

                  tr.attr('id', 'category-order-' + (order - 1))
                  btn.attr('data-order', order - 1)
                  tr.find('.move-down').attr('data-order', order - 1)
                  trAbove.attr('id', 'category-order-' + order)
                  trAbove.find('.move-up').attr('data-order', order)
                  trAbove.find('.move-down').attr('data-order', order)

                  $('#cat-' + id).after($('#cat-' + idAbove))

                  btn.attr('disabled', false).html('<i class=\'fas fa-arrow-up\'></i>')
                  tr.effect('highlight', {}, 2000)
                })
            })

        })
        $('.move-down').click(function () {
          let btn     = $(this),
              id      = btn.data('id'),
              order   = parseInt(btn.attr('data-order')),
              tr      = $('tr#category-order-' + order),
              trBelow = $('tr#category-order-' + (order + 1)),
              idBelow = trBelow.data('cat-id')

          if (order == {{ $categories->count() }}) return null

          tr.stop(true, true)

          console.log('This Order: ' + order)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/updateCategory/' + id,
            'method': 'PUT',
            'data'  : {'order': order + 1}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move category. ' + error, 'error')
            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/updateCategory/' + idBelow,
                'method': 'PUT',
                'data'  : {'order': order}
              })
                .error(function (xhr, status, error) {
                  swal('Error!', 'Unable to move category. ' + error, 'error')
                })
                .done(function (result) {
                  trBelow.after(tr)
                  if (!order) {
                    tr.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-up').addClass('hidden')
                    trBelow.find('.move-down').removeClass('hidden')
                  }

                  if (order + 2 == {{ $categories->count() }}) {
                    btn.addClass('hidden')
                    tr.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-down').removeClass('hidden')
                  }

                  tr.find('td:first-child').text(order + 2)
                  trBelow.find('td:first-child').text(order + 1)

                  tr.attr('id', 'category-order-' + (order + 1))
                  btn.attr('data-order', order + 1)
                  tr.find('.move-up').attr('data-order', order + 1)
                  trBelow.attr('id', 'category-order-' + order)
                  trBelow.find('.move-up').attr('data-order', order)
                  trBelow.find('.move-down').attr('data-order', order)

                  $('#cat-' + idBelow).after($('#cat-' + id))

                  btn.attr('disabled', false).html('<i class=\'fas fa-arrow-down\'></i>')
                  tr.effect('highlight', {}, 2000)
                })
            })

        })
      })
    </script>
@endsection