@extends('layout')
@section('title', 'Policies Management')
@section('content')
    <!-- New Policy Modal -->
    <div class="modal fade" id="new-policy-modal" tabindex="-1" role="dialog" aria-labelledby="Create New Policy">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="new-policy-modal-title">Upload New <span
                            id="new-policy-category"></span> Policy</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="new-policy-form">
                        <input type="hidden" id="new-policy-category-id" name="category">
                        <div class="form-group">
                            <label for="ident" class="col-sm-3 control-label">Ident & Title</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="new-policy-ident" placeholder="DP001"
                                       name="ident" style="display: inline; margin-right:10px; width:20%;"
                                       required>-<input
                                    style="display: inline; margin-left:10px; width:60%" type="text"
                                    class="form-control" name="title"
                                    id="new-policy-title" placeholder="Policy Title" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="slug" class="col-sm-3 control-label">Policy URL</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon">vatusa.net/docs/</div>
                                    <input type="text" class="form-control" name="slug" id="new-policy-slug"
                                           placeholder="new-document-name" style="width:79%" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="policy-permissions" class="col-sm-3 control-label">Permissions</label>
                            <div class="col-sm-offset-3 col-sm-9" id="policy-permissions">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="0" class="perm-checkbox"> Public
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="1" class="perm-checkbox"> Home
                                        Controllers Only
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="2" class="perm-checkbox">
                                        Webmasters
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="3" class="perm-checkbox"> Event
                                        Coordinators
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="4" class="perm-checkbox"> Facility
                                        Engineers
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="5" class="perm-checkbox"> Mentors
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="6" class="perm-checkbox">
                                        Instructors
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="7" class="perm-checkbox"> Training
                                        Administrators
                                    </label>
                                </div>

                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="8" class="perm-checkbox"> Deputy
                                        Air Traffic Managers
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="9" class="perm-checkbox"> Air
                                        Traffic Managers
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="new-policy-effective-date" class="col-sm-3 control-label">Effective
                                    Date</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="effective"
                                           id="new-policy-effective-date" style="width: 40%" placeholder="__/__/____">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="new-policy-file" class="col-sm-3 control-label">File Upload</label>
                                <div class="col-sm-9">
                                    <input type="file" id="new-policy-file" style="width:60%">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="policy-submit"><i class="fas fa-check"></i> Submit
                        New Policy
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                                <input type="hidden" id="cat-policy-count-{{ $category->id }}"
                                       value="{{ $category->policies()->count() }}">
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
                                            <tr data-policy-id="{{ $policy->id }}"
                                                id="policy-order-{{ $policy->order }}">
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
                                                            data-cat-id="{{ $policy->category }}"
                                                            data-order="{{ $policy->order }}"><i
                                                                class="fas fa-arrow-up"></i>
                                                        </button>
                                                        <button
                                                            class="btn btn-primary policy-move move-down @if($policy->order + 1 == $category->policies->count()) hidden @endif"
                                                            data-id="{{ $policy->id }}"
                                                            data-cat-id="{{ $policy->category }}"
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
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td colspan="4">
                                                <button class="btn btn-success new-policy"
                                                        id="new-policy-{{ $category->id }}"
                                                        data-cat-name="{{ $category->name }}"
                                                        data-cat-id="{{ $category->id }}"><i
                                                        class="fas fa-plus"></i> <span>Add
                                                        New Policy</span>
                                                </button>
                                            </td>
                                        </tr>
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
@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ secure_asset("datetimepicker/datetimepicker.css") }}">
@endpush
@section('scripts')
    <script type="text/javascript" src="{{ secure_asset("datetimepicker/datetimepicker.js") }}"></script>
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
              $('#new-policy-' + id).find('span').attr('data-cat-name', value)
              swal('Success!', 'The category has been edited.', 'success')
            })

          btn.attr('disabled', false).html('<i class=\'fas fa-check\'></i>')
        })

        $('.move-up').click(function () {
          let btn     = $(this),
              id      = btn.data('id'),
              order   = parseInt(btn.attr('data-order')),
              type    = $(this).hasClass('policy-move') ? 'policy' : 'category',
              tr      = $('tr#' + type + '-order-' + order),
              cat     = btn.data('cat-id'),
              trAbove = $('tr#' + type + '-order-' + (order - 1)),
              idAbove = trAbove.data(type === 'policy' ? 'policy-id' : 'cat-id')

          if (!order) return null

          tr.stop(true, true)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + id,
            'method': 'PUT',
            'data'  : {'order': order - 1}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')

            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + idAbove,
                'method': 'PUT',
                'data'  : {'order': order}
              })
                .error(function (xhr, status, error) {
                  swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')

                })
                .done(function (result) {
                    tr.after(trAbove)
                    if (order - 1 == 0) {
                      btn.addClass('hidden')
                    }
                    tr.find('.move-down').removeClass('hidden')
                    trAbove.find('.move-up').removeClass('hidden')
                    if (type === 'category' && order + 1 == {{ $categories->count() }} || type === 'policy' && order + 1 == $('#cat-policy-count-' + cat).val())
                      trAbove.find('.move-down').addClass('hidden')
                    else
                      trAbove.find('.move-down').removeClass('hidden')

                    tr.find('td:first-child').text(order)
                    trAbove.find('td:first-child').text(order + 1)

                    tr.attr('id', type + '-order-' + (order - 1))
                    btn.attr('data-order', order - 1)
                    tr.find('.move-down').attr('data-order', order - 1)
                    trAbove.attr('id', type + '-order-' + order)
                    trAbove.find('.move-up').attr('data-order', order)
                    trAbove.find('.move-down').attr('data-order', order)

                    if (type === 'category') $('#cat-' + id).after($('#cat-' + idAbove))
                    else $('#policy-' + id).after($('#policy-' + idAbove))

                    btn.attr('disabled', false).html('<i class=\'fas fa-arrow-up\'></i>')
                    tr.effect('highlight', {}, 2000)
                  }
                )
            })

        })
        $('.move-down').click(function () {
          let btn     = $(this),
              id      = btn.data('id'),
              order   = parseInt(btn.attr('data-order')),
              type    = $(this).hasClass('policy-move') ? 'policy' : 'category',
              tr      = $('tr#' + type + '-order-' + order),
              cat     = btn.data('cat-id'),
              trBelow = $('tr#' + type + '-order-' + (order + 1)),
              idBelow = trBelow.data(type === 'policy' ? 'policy-id' : 'cat-id')

          if (type === 'category' && order == {{ $categories->count() }} || type === 'policy' && order == $('#cat-policy-count-' + cat).val()) return null

          tr.stop(true, true)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + id,
            'method': 'PUT',
            'data'  : {'order': order + 1}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')
            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + idBelow,
                'method': 'PUT',
                'data'  : {'order': order}
              })
                .error(function (xhr, status, error) {
                  swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')
                })
                .done(function (result) {
                  trBelow.after(tr)
                  if (!order) {
                    tr.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-up').addClass('hidden')
                    trBelow.find('.move-down').removeClass('hidden')
                  }

                  if (type === 'category' && order + 2 == {{ $categories->count() }} || type === 'policy' && order + 2 == $('#cat-policy-count-' + cat).val()) {
                    btn.addClass('hidden')
                    tr.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-up').removeClass('hidden')
                    trBelow.find('.move-down').removeClass('hidden')
                  }

                  tr.find('td:first-child').text(order + 2)
                  trBelow.find('td:first-child').text(order + 1)

                  tr.attr('id', type + '-order-' + (order + 1))
                  btn.attr('data-order', order + 1)
                  tr.find('.move-up').attr('data-order', order + 1)
                  trBelow.attr('id', type + '-order-' + order)
                  trBelow.find('.move-up').attr('data-order', order)
                  trBelow.find('.move-down').attr('data-order', order)

                  if (type === 'category') $('#cat-' + idBelow).after($('#cat-' + id))
                  else $('#policy-' + idBelow).after($('#policy-' + id))

                  btn.attr('disabled', false).html('<i class=\'fas fa-arrow-down\'></i>')
                  tr.effect('highlight', {}, 2000)
                })
            })

        })

        $('.new-policy').click(function () {
          let btn     = $(this),
              catId   = btn.data('cat-id'),
              catName = btn.data('cat-name')

          $('#new-policy-category').text(catName)
          $('#new-policy-category-id').val(catId)

          $('#new-policy-effective-date').datetimepicker({
            timepicker: false,
            mask      : true,
            format    : 'm/d/Y'
          })

          $('#new-policy-form')[0].reset()
          $('input[type=checkbox]').prop('disabled', false)
          $('#new-policy-modal').modal('toggle')
        })
      })
      $('#new-policy-title').on('keyup', function () {
        let val = $(this).val()

        $('#new-policy-slug').val(val.toLowerCase().replace(/[^\w -]+/g, '').replace(/ +/g, '-'))
      })
      $('#new-policy-slug').on('keyup', function () {
        let val = $(this).val()

        $(this).val(val.toLowerCase().replace(/[^\w -]+/g, '').replace(/ +/g, '-'))
      })

      $('.perm-checkbox').change(function () {
        let box = $(this),
            val = box.val()
        let overrides = {
          0: [1, 2, 3, 4, 5, 6, 7, 8, 9],
          1: [2, 3, 4, 5, 6, 7, 8, 9],
          2: [8, 9],
          3: [8, 9],
          4: [8, 9],
          5: [6, 7, 8, 9],
          6: [7, 8, 9],
          7: [8, 9],
          8: [9],
        }
        if (overrides[val] !== undefined)
          $.each(overrides[val], function (i, ival) {
            let checkbox = $('.perm-checkbox[value="' + ival + '"]'),
                state    = box.prop('checked')
            checkbox.attr('disabled', state).prop('checked', state)
          })
      })

      $('#policy-submit').click(function () {
        let btn      = $(this),
            form     = $('#new-policy-form')[0],
            file     = $('#new-policy-file')[0].files,
            formData = new FormData(form)
        btn.prop('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i> Submitting...')
        formData.append('file', file[0])

        if (!$('#new-policy-ident').val().length || !$('#new-policy-title').val().length || !$('#new-policy-slug').val().length || !$('.perm-checkbox:checked').length || !file.length) {
          btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit New Policy')
          return swal('Error!', 'All inputs (except Effective Date) are required.', 'error')
        }

        $.ajax({
          url        : '/mgt/policies/store',
          method     : 'POST',
          data       : formData,
          processData: false,
          contentType: false
        }).done(response => {
          if (parseInt(response)) {
            btn.html('<i class=\'fas fa-check\'></i> Submitted')
            return (swal('Success!', 'The policy has been successfully uploaded.', 'success').then(_ => location.reload()))
          }

          btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit New Policy')
          swal('Error!', 'Unable to upload policy. ' + response, 'error')
        }).error((xhr, status, error) => {
          btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit New Policy')
          swal('Error!', 'Unable to upload policy. ' + error, 'error')
        })

      })
    </script>
@endsection