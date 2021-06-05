@extends('layout')
@section('title', 'Policies & Downloads Management')
@section('content')
    <!-- New Policy Modal -->
    <div class="modal fade" id="new-policy-modal" tabindex="-1" role="dialog" aria-labelledby="Create New Policy">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="new-policy-modal-title-line"><span
                                id="new-policy-title-line" class="new-policy-objects">Upload New</span><span
                                id="edit-policy-title-line" class="edit-policy-objects">Edit Existing</span>
                        <span
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
                                       maxlength="10"
                                       required>&mdash;<input
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
                                           placeholder="new-document-name" style="width:81%" required>
                                </div>
                                <p class="help-block edit-policy-objects"><i class="fas fa-info-circle"></i> To change
                                    the Policy URL, you must delete the policy and create a new one.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="slug" class="col-sm-3 control-label">Short Description</label>
                            <div class="col-sm-9">
                                <input style="width:87%" type="text" class="form-control" name="desc"
                                       id="new-policy-desc" placeholder="Policy Description">
                            </div>
                        </div>
                        <div class="form-group edit-policy-objects">
                            <label for="new-policy-category-input" class="col-sm-3 control-label">Category</label>
                            <div class="col-sm-9">
                                <select style="width:87%" class="form-control" name="category_edit"
                                        id="new-policy-category-input">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
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
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="perms[]" value="10" class="perm-checkbox">VATUSA
                                        Staff
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
                        <div class="form-group edit-policy-objects">
                            <div class="form-group">
                                <label for="new-policy-clear-modified" class="col-sm-3 control-label">Options</label>
                                <div class="col-sm-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="clear_modified" value="1"
                                                   id="new-policy-clear-modified">Clear Modified Date
                                        </label>
                                    </div>
                                    <p class="help-block">This will set the Modified Date to be the same as the
                                        Effective Date.</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="new-policy-file" class="col-sm-3 control-label new-policy-objects">File
                                    Upload</label>
                                <label for="new-policy-file" class="col-sm-3 control-label edit-policy-objects">Replacement
                                    File
                                    Upload</label>
                                <div class="col-sm-9">
                                    <input type="file" id="new-policy-file" style="width:60%">
                                    <p class="help-block edit-policy-objects">If no file is uploaded, the previous file
                                        will remain.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success new-policy-objects" id="policy-submit"><i
                                class="fas fa-check"></i> Submit
                        New Policy
                    </button>
                    <button type="button" class="btn btn-success edit-policy-objects" id="policy-edit-submit"><i
                                class="fas fa-check"></i>
                        Submit
                        Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Policies & Downloads Management
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
                                                id="policy-order-{{ $policy->order }}"
                                                @if(!$policy->visible) class="text-muted"@endif>
                                                <td>{{ $policy->order + 1 }}</td>
                                                <td>{{ $policy->ident }}
                                                    <br><em>{{ strtoupper($policy->extension) }}</em></td>
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
                                                                data-cat-name="{{ $category->name }}"
                                                                data-cat-id="{{ $category->id }}"
                                                                data-name="{{ $policy->title }}"><i
                                                                    class="fas fa-pencil-alt"></i></button>
                                                        <button class="btn btn-danger delete-policy"
                                                                data-id="{{ $policy->id }}"
                                                                data-name="{{ $policy->title }}"><i
                                                                    class="fas fa-times"></i></button>
                                                    </div>
                                                    @if($policy->visible)
                                                        <button class="btn btn-success toggle-visible"
                                                                data-id="{{ $policy->id }}"
                                                                style="transition: all 0.4s ease"><i
                                                                    class="fas fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-default toggle-visible"
                                                                style="transition: all 0.4s ease"
                                                                data-id="{{ $policy->id }}"><i
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
    <script src="{{ secure_asset("js/moment.js") }}" type="text/javascript"></script>

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
            closeModal: false
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
              tr      = btn.closest('table').find('tr#' + type + '-order-' + order),
              cat     = btn.data('cat-id'),
              trAbove = btn.closest('table').find('tr#' + type + '-order-' + (order - 1)),
              idAbove = trAbove.data(type === 'policy' ? 'policy-id' : 'cat-id')

          if (!order) return null

          tr.stop(true, true)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + id,
            'method': type === 'category' ? 'PUT' : 'POST',
            'data'  : {order: order - 1, clear_modified: true}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')

            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + idAbove,
                'method': type === 'category' ? 'PUT' : 'POST',
                'data'  : {order: order, clear_modified: true}
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

                    if (type === 'category') btn.closest('table').find('#cat-' + id).after($('#cat-' + idAbove))
                    else btn.closest('table').find('#policy-' + id).after($('#policy-' + idAbove))

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
              tr      = btn.closest('table').find('tr#' + type + '-order-' + order),
              cat     = btn.data('cat-id'),
              trBelow = btn.closest('table').find('tr#' + type + '-order-' + (order + 1)),
              idBelow = trBelow.data(type === 'policy' ? 'policy-id' : 'cat-id')

          if (type === 'category' && order == {{ $categories->count() }} || type === 'policy' && order == $('#cat-policy-count-' + cat).val()) return null

          tr.stop(true, true)

          btn.attr('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')

          $.ajax({
            'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + id,
            'method': type === 'category' ? 'PUT' : 'POST',
            'data'  : {order: order + 1, clear_modified: true}
          })
            .error(function (xhr, status, error) {
              swal('Error!', 'Unable to move ' + type + '. ' + error, 'error')
            })
            .done(function (result) {
              $.ajax({
                'url'   : '/mgt/policies/update' + (type === 'category' ? 'Category' : 'Policy') + '/' + idBelow,
                'method': type === 'category' ? 'PUT' : 'POST',
                'data'  : {order: order, clear_modified: true}
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

                  if (type === 'category') btn.closest('table').find('#cat-' + idBelow).after($('#cat-' + id))
                  else btn.closest('table').find('#policy-' + idBelow).after($('#policy-' + id))

                  btn.attr('disabled', false).html('<i class=\'fas fa-arrow-down\'></i>')
                  tr.effect('highlight', {}, 2000)
                })
            })

        })

        $('.new-policy').click(function () {
          let btn     = $(this),
              catId   = btn.data('cat-id'),
              catName = btn.data('cat-name')

          $('.new-policy-objects').show()
          $('.edit-policy-objects').hide()
          $('#new-policy-slug').prop('disabled', false)

          $('#new-policy-form')[0].reset()
          $('#new-policy-category').text(catName)
          $('#new-policy-category-id').val(catId)

          $('#new-policy-effective-date').datetimepicker({
            timepicker: false,
            mask      : true,
            format    : 'm/d/Y'
          })

          $('input[type=checkbox]').prop('disabled', false)
          $('#new-policy-modal').modal('toggle')
        })
        $('.edit-policy').click(function () {
          let btn     = $(this),
              id      = $(this).data('id'),
              catId   = btn.data('cat-id'),
              catName = btn.data('cat-name')
          btn.prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>')
          $.get('/mgt/policies/getInfo/' + id, response => {
            if (response.hasOwnProperty('id')) {
              $('#new-policy-form')[0].reset()
              $('#new-policy-category').text(catName)
              $('#new-policy-category-id').val(catId)

              $('.new-policy-objects').hide()
              $('.edit-policy-objects').show()
              $('#policy-edit-submit').attr('data-id', id)

              $('#new-policy-effective-date').datetimepicker({
                timepicker: false,
                mask      : true,
                format    : 'm/d/Y'
              })

              $('input[type=checkbox]').prop('disabled', false)

              $('#new-policy-ident').val(response.ident)
              $('#new-policy-title').val(response.title)
              $('#new-policy-slug').val(response.slug).prop('disabled', true)
              $('#new-policy-desc').val(response.description ?? '')
              $('#new-policy-category-input').val(response.category)

              let perms     = response.perms.split('|'),
                  overrides = {
                    0: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                    1: [2, 3, 4, 5, 6, 7, 8, 9, 10],
                    2: [8, 9, 10],
                    3: [8, 9, 10],
                    4: [8, 9, 10],
                    5: [6, 7, 8, 9, 10],
                    6: [7, 8, 9, 10],
                    7: [8, 9, 10],
                    8: [9, 10],
                    9: [10]
                  }
              $.each(perms, (index, perm) => {
                $('.perm-checkbox[value=' + perm + ']').prop('checked', true)

                if (overrides[perm] !== undefined)
                  $.each(overrides[perm], function (i, ival) {
                    let checkbox = $('.perm-checkbox[value="' + ival + '"]'),
                        state    = true
                    checkbox.attr('disabled', state).prop('checked', state)
                  })
              })
              $('#new-policy-effective-date').val(moment(response.effective_date).format('MM/DD/YYYY'))
              $('#new-policy-modal').modal('toggle')
              btn.prop('disabled', false).html('<i class="fas fa-pencil-alt"></i>')
            } else return swal('Error!', 'Could not get policy details.', 'error')
          })
        })
        $('.delete-policy').click(function () {
          swal({
            title     : 'Are you sure you want to delete "' + $(this).data('name') + '"?',
            text      : 'This will also delete the file from the server and cannot be undone.',
            icon      : 'warning',
            buttons   : true,
            dangerMode: true,
            closeModal: false
          })
            .then((willDelete) => {
              if (willDelete) {
                $.ajax({
                  url   : '/mgt/policies/' + $(this).data('id'),
                  method: 'DELETE'
                })
                  .done(resp => {
                    if (parseInt(resp)) {
                      $('tr[data-policy-id="' + $(this).data('id') + '"]').remove()
                      return swal('Success!', 'The policy has been deleted.', 'success')
                    }
                    swal('Error!', 'The policy could not be deleted.', 'error')
                  })
                  .error((xhr, status, error) => {
                    swal('Error!', 'The policy could not be deleted. ' + xhr.responseJSON.message, 'error')
                  })
              } else return false
            })
        })
        $('.toggle-visible').click(function () {
          let btn       = $(this),
              isVisible = btn.hasClass('btn-success')
          btn.prop('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i>')
          $.ajax({
            url   : '/mgt/policies/updatePolicy/' + btn.data('id'),
            method: 'POST',
            data  : {visible: !isVisible, clear_modified: true}
          }).done(response => {
            if (parseInt(response)) {
              btn.prop('disabled', false).html('<i class=\'fas fa-eye\'></i>')
              if (isVisible) {
                btn.removeClass('btn-success').addClass('btn-default')
                btn.find('i').removeClass('fas fa-eye').addClass('fas fa-eye-slash')
                btn.parents('tr').addClass('text-muted')
              } else {
                btn.attr('title', 'Make Visible')
                btn.find('i').removeClass('fas fa-eye-slash').addClass('fas fa-eye')
                btn.removeClass('btn-default').addClass('btn-success')
                btn.parents('tr').removeClass('text-muted')
              }
            } else swal('Error!', 'Unable to toggle visibility.', 'error')
          }).error((xhr, status, error) => {
            btn.prop('disabled', false).html('<i class=\'fas fa-eye\'></i>')
            swal('Error!', 'Unable to toggle visibility. ' + xhr.responseJSON.message, 'error')
          })
        })

        $('#new-policy-title').on('keyup', function () {
          if ($('#new-policy-slug').prop('disabled')) return false
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
            0: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            1: [2, 3, 4, 5, 6, 7, 8, 9, 10],
            2: [8, 9, 10],
            3: [8, 9, 10],
            4: [8, 9, 10],
            5: [6, 7, 8, 9, 10],
            6: [7, 8, 9, 10],
            7: [8, 9, 10],
            8: [9, 10],
            9: [10]
          }
          if (overrides[val] !== undefined) {
            $.each(overrides[val], function (i, ival) {
              let checkbox = $('.perm-checkbox[value="' + ival + '"]'),
                  state    = box.prop('checked')
              checkbox.attr('disabled', state).prop('checked', state)
            })
          }
          $('.perm-checkbox:checked').each(function () {
            if (overrides[$(this).val()] !== undefined) {
              $.each(overrides[$(this).val()], function (i, ival) {
                let checkbox = $('.perm-checkbox[value="' + ival + '"]'),
                    state    = true
                checkbox.attr('disabled', state).prop('checked', state)
              })
            }
          })
        })

        $('#policy-submit').click(function () {
          let btn      = $(this),
              form     = $('#new-policy-form')[0],
              file     = $('#new-policy-file')[0].files,
              formData = new FormData(form)
          btn.prop('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i> Submitting...')
          formData.append('file', file[0])

          if (!$('#new-policy-ident').val().length || !$('#new-policy-title').val().length || !$('#new-policy-slug').val().length || !$('.perm-checkbox:checked').length || !$('#new-policy-effective-date').val().length || !file.length) {
            btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit New Policy')
            return swal('Error!', 'All inputs are required.', 'error')
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
            swal('Error!', 'Unable to upload policy. ' + xhr.responseJSON.message, 'error')
          })

        })
        $('#policy-edit-submit').click(function () {
          let btn      = $(this),
              id       = $(this).data('id'),
              form     = $('#new-policy-form')[0],
              file     = $('#new-policy-file')[0].files,
              formData = new FormData(form)
          btn.prop('disabled', true).html('<i class=\'fas fa-spin fa-spinner\'></i> Submitting...')
          formData.append('file', file[0])

          if (!$('#new-policy-ident').val().length || !$('#new-policy-title').val().length || !$('#new-policy-slug').val().length || !$('#new-policy-effective-date').val().length || !$('.perm-checkbox:checked').length) {
            btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit Changes')
            return swal('Error!', 'All inputs are required.', 'error')
          }

          $.ajax({
            url        : '/mgt/policies/updatePolicy/' + id,
            method     : 'POST',
            data       : formData,
            processData: false,
            contentType: false
          }).done(response => {
            if (parseInt(response)) {
              btn.html('<i class=\'fas fa-check\'></i> Submitted')
              return (swal('Success!', 'The policy has been successfully edited.', 'success').then(_ => location.reload()))
            }

            btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit Changes')
            swal('Error!', 'Unable to edit policy. ' + response, 'error')
          }).error((xhr, status, error) => {
            btn.prop('disabled', false).html('<i class=\'fas fa-check\'></i> Submit Changes')
            swal('Error!', 'Unable to edit policy. ' + xhr.responseJSON.message, 'error')
          })

        })
      })
    </script>
@endsection