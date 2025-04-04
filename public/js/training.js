$(function () {

  $('#pos-types li a').click(function (e) {
    e.preventDefault()
    let target = $(this).data('controls')
    $('#training-content div[role="tabpanel"]#' + target).show()
    $('#training-content div[role="tabpanel"]:not(#' + target + ')').hide()
  })

  if (parseInt($('#canAdd').val())) {
    $('.training-records-list').DataTable({
      responsive  : true,
      autoWidth   : false,
      lengthMenu  : [5, 10, 15, 25],
      dom         : '<\'row\'<\'col-sm-12 col-md-4\'l><\'col-sm-12 col-md-4\'B><\'col-sm-12 col-md-4\'f>>' +
        '<\'row\'<\'col-sm-12\'tr>>' +
        '<\'row\'<\'col-sm-12 col-md-5\'i><\'col-sm-12 col-md-7\'p>>',
      buttons     : [
        {
          text     : '<span class=\'glyphicon glyphicon-plus\'></span> Add New Record',
          className: 'btn btn-success',
          action   : (e, dt, node, config) => {
            showTrainingRecordModal($(this))
            // $('#e-training-notes').html(result.notes)
          }
        }],
      pageLength  : 10,
      columnDefs  : [{
        visible: false,
        targets: 6
      }],
      order       : [[0, 'desc']],
      drawCallback: function (settings) {
        let api = this.api()
        let rows = api.rows({page: 'current'}).nodes()
        let last = null

        api.column(6, {page: 'current'}).data().each(function (group, i) {
          if (last !== group) {
            $(rows).eq(i).before(
              '<tr class="group"><td colspan="6"><strong>' + group + '</strong></td></tr>'
            )

            last = group
          }
        })
      }
    })
  } else {
    $('.training-records-list').DataTable({
      responsive  : true,
      autoWidth   : false,
      lengthMenu  : [5, 10, 15, 25],
      pageLength  : 10,
      columnDefs  : [{
        visible: false,
        targets: 6
      }],
      order       : [[0, 'desc']],
      drawCallback: function (settings) {
        let api = this.api()
        let rows = api.rows({page: 'current'}).nodes()
        let last = null

        api.column(6, {page: 'current'}).data().each(function (group, i) {
          if (last !== group) {
            $(rows).eq(i).before(
              '<tr class="group"><td colspan="6"><strong>' + group + '</strong></td></tr>'
            )

            last = group
          }
        })
      }
    })
    $('.fac-training-records-list').DataTable({
      responsive  : true,
      autoWidth   : false,
      lengthMenu  : [5, 10, 15, 25],
      pageLength  : 10,
      columnDefs  : [{
        visible: false,
        targets: 6
      }],
      order       : [[0, 'desc']],
      //orderFixed  : [[6, 'asc']],
      drawCallback: function (settings) {
        let api = this.api()
        let rows = api.rows({page: 'current'}).nodes()
        let last = null

        api.column(6, {page: 'current'}).data().each(function (group, i) {
          if (last !== group) {
            $(rows).eq(i).before(
              '<tr class="group"><td colspan="6"><strong>' + group + '</strong></td></tr>'
            )

            last = group
          }
        })
      }
    })
  }
  $('.training-evals-list').DataTable({
    responsive  : true,
    autoWidth   : false,
    lengthMenu  : [5, 10, 15, 25],
    pageLength  : 10,
    order       : [[0, 'desc']],
    columnDefs  : [{
      visible: false,
      targets: 1
    }],
    orderFixed  : [1, 'asc'],
    drawCallback: function (settings) {
      let api = this.api()
      let rows = api.rows({page: 'current'}).nodes()
      let last = null

      api.column(1, {page: 'current'}).data().each(function (group, i) {
        if (last !== group) {
          $(rows).eq(i).before(
            '<tr class="group"><td colspan="6"><strong>' + group + '</strong></td></tr>'
          )

          last = group
        }
      })
    }
  })

  $(document).on('click', '.delete-tr', function () {
    let btn = $(this),
        id  = btn.data('id'),
        tr  = btn.parents('tr')
    let alertText = document.createElement('table')
    alertText.className = 'table table-border'
    alertText.innerHTML = $('.training-records-list').first().children('thead').ignore('.alert-ignore').html()
      + '<tbody><tr>' + tr.ignore('.alert-ignore').html() + '</tr></tbody>'

    swal({
      title     : 'Deleting Training Record',
      content   : alertText,
      text      : 'Are you sure you want to delete the training record?',
      icon      : 'warning',
      buttons   : {
        cancel : {
          text   : 'No, cancel',
          visible: true,
        },
        confirm: {
          text      : 'Yes, delete',
          closeModal: false
        }
      },
      dangerMode: true,
    })
      .then(willDelete => {
        if (willDelete) {
          $.ajax({
            url   : $.apiUrl() + '/v2/training/record/' + id,
            method: 'DELETE'
          }).success(() => {
            swal('Success!', 'The training record has been deleted.', 'success')
            $('.training-records-list').DataTable().row(tr).remove().draw()
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })

  })
  $(document).on('click', '.view-tr', function () {
    let btn = $(this),
        id  = btn.data('id')

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)

    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'GET',
    }).done(resp => {
      $.ajax({
        url: '/mgt/controller/ajax/canModifyRecord/' + id
      }).done(cm => {
        if (cm) {
          $('#v-modify-group').css('display', 'inline-block')
          $('#tr-view-delete').attr('data-id', id)
          $('#tr-view-edit').attr('data-id', id)
        } else {
          $('#v-modify-group').hide()
          $('#tr-view-delete').attr('data-id', '')
          $('#tr-view-edit').attr('data-id', '')
        }

        let result = resp.data
        $('.training-position').html(result.position)
        $('.training-student').html(result.student.fname + ' ' + result.student.lname)
        $('#training-artcc').html(result.facility.name)
        scoreStr = ''
        if (!isNaN(result.score))
          for (let i = 1; i <= 5; i++) {
            scoreStr += '<span class=\'glyphicon glyphicon-star'
            scoreStr += i > result.score ? '-empty' : ''
            scoreStr += '\'></span> &nbsp;'
          }
        $('#training-score').html(scoreStr)
        $('#training-datetime').html(moment(result.session_date).format('dddd, MMMM Do YYYY, hh:mm'))
        let duration    = moment.duration(result.duration),
            durHours    = duration.hours(),
            durMinutes  = duration.minutes(),
            durationStr = ''
        if (durHours > 0) durationStr += durHours + ' hour' + ((durHours > 1 ? 's' : '')) + ' '
        if (durMinutes > 0) durationStr += durMinutes + ' minute' + ((durMinutes > 1 ? 's' : ''))
        $('#training-duration').html(durationStr)
        $('#training-movements').html(!isNaN(parseInt(result.movements)) ? result.movements : '<em>Not Available</em>')
        let location = ''
        switch (result.location) {
          case 0:
            location = 'Classroom'
            break
          case 1:
            location = 'Live'
            break
          case 2:
            location = 'Sweatbox'
            break
          default:
            location = '<em>Not Available</em>'
            break
        }
        $('#training-location').html(location)
        $('#training-instructor').html(Object.getOwnPropertyNames(result.instructor).length ? result.instructor.fname + ' ' + result.instructor.lname : '<em>Account Erased</em>')
        $('#training-notes').html(result.notes)

        $('#training-ots-exam').hide()
        $('#training-ots-exam-pass').hide()
        $('#training-ots-exam-fail').hide()
        $('#training-ots-exam-rec').hide()
        if (result.ots_status)
          $('#training-ots-exam').show()
        switch (result.ots_status) {
          case 1:
            $('#training-ots-exam-pass').show()
            if (result.ots_eval_id && $('#canAdd').length) $('#training-ots-exam-pass')
              .attr('title', 'View OTS Evaluation').css('cursor', 'pointer').click(function () {
                document.location = '/mgt/facility/training/eval/' + result.ots_eval_id + '/view'
              })
            else $('#training-ots-exam-pass')
              .attr('title', '').off('click').css('cursor', 'default')
            break
          case 2:
            $('#training-ots-exam-fail').show()
            if (result.ots_eval_id && $('#canAdd').length) $('#training-ots-exam-fail')
              .attr('title', 'View OTS Evaluation').css('cursor', 'pointer').click(function () {
                document.location = '/mgt/facility/training/eval/' + result.ots_eval_id + '/view'
              })
            else $('#training-ots-exam-fail')
              .attr('title', '').off('click').css('cursor', 'default')
            break
          case 3:
            $('#training-ots-exam-rec').show()
            break
          default:
            $('#training-ots-exam').hide()
            break
        }
        btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>' + (!$('#canAdd').length ? ' View' : '')).attr('disabled', false)
        $('#view-training-record').modal('show')
      }).fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>' + (!$('#canAdd').length ? ' View' : '')).attr('disabled', false)
        $('#v-modify-group').hide()
        $('#tr-view-delete').attr('data-id', '')
        $('#tr-view-edit').attr('data-id', '')
      })
    })
      .fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>' + (!$('#canAdd').length ? ' View' : '')).attr('disabled', false)
        swal('Error!', 'Unable to get training record. ' + error, 'error')
      })
  })
  $(document).on('click', '.edit-tr', function () {
    let btn = $(this),
        id  = btn.data('id')

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)
    $('#tr-view-edit').html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)
    $('.ots-exam-warning').hide();

    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'GET',
    }).done(resp => {
      let result = resp.data
      btn.html('<span class=\'glyphicon glyphicon-pencil\'></span>').attr('disabled', false)
      $('#tr-view-edit').html('<span class=\'glyphicon glyphicon-pencil\'></span> Edit').attr('disabled', false)

      $('#edit-training-record .tr-modal-delete, #e-training-submit').attr('data-id', id)
      $('input.e-training-position').val(result.position)
      $('span.e-training-position').html(result.position)
      $('#e-training-student').html(result.student.fname + ' ' + result.student.lname)
      $('#e-training-artcc').html(result.facility.name)
      $('#e-training-score').val(result.score)
      $('#e-training-datetime').val(moment(result.session_date).format('YYYY-MM-DD HH:mm'))
      $('#e-training-duration-hrs').val(moment.duration(result.duration).hours())
      let mins = moment.duration(result.duration).minutes()
      if (mins < 10) mins = '0' + mins
      $('#e-training-duration-mins').val(mins)
      $('#e-training-movements').val(result.movements)
      $('#e-training-location').val(result.location)
      $('#e-training-instructor').html(Object.getOwnPropertyNames(result.instructor).length ? result.instructor.fname + ' ' + result.instructor.lname : '<em>Account Erased</em>')
      $('#e-ots-status-' + result.ots_status).parent().button('toggle')

      $('#e-training-datetime').datetimepicker({
        timepicker: true,
        format    : 'Y-m-d H:i',
        mask      : true,
        maxDate   : '+1970/01/02',
        step      : 15
      })

      tinymce.init({
        selector                     : '#e-training-notes',
        plugins                      : 'preview paste importcss searchreplace autolink autosave save visualblocks visualchars fullscreen image link media template table charmap hr nonbreaking toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap emoticons',
        imagetools_cors_hosts        : ['picsum.photos'],
        menubar                      : 'file edit view insert format tools table help',
        toolbar                      : 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap emoticons | fullscreen preview save | image media link',
        toolbar_sticky               : false,
        autosave_ask_before_unload   : true,
        autosave_interval            : '30s',
        autosave_prefix              : '{path}{query}-{id}-',
        autosave_restore_when_empty  : false,
        autosave_retention           : '2m',
        image_advtab                 : true,
        importcss_append             : true,
        height                       : 600,
        image_caption                : true,
        noneditable_noneditable_class: 'mceNonEditable',
        toolbar_mode                 : 'sliding',
        contextmenu                  : 'link image imagetools table',
        setup                        : editor => {
          editor.on('init', e => {
            editor.setContent(result.notes)
          })
        }
      })
      $('#view-training-record').modal('hide')
      $('#edit-training-record').modal('show')

    })
      .fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-pencil\'></span>').attr('disabled', false)
        $('#tr-view-edit').html('<span class=\'glyphicon glyphicon-pencil\'></span> Edit').attr('disabled', false)
        swal('Error!', 'Unable to get training record. ' + error, 'error')
      })

  })

  $('#edit-training-record').on('hidden.bs.modal', function () {
    tinymce.get('e-training-notes').remove()
  })
  $('#new-training-record').on('hidden.bs.modal', function () {
    tinymce.get('n-training-notes').remove()
  })

  $('#tr-view-delete').click(function () {
    let btn = $(this),
        id  = btn.data('id'),
        tr  = $('.delete-tr[data-id=' + id + ']').parents('tr')
    let alertText = document.createElement('table')
    alertText.className = 'table table-border'
    alertText.innerHTML = $('.training-records-list').first().children('thead').ignore('.alert-ignore').html()
      + '<tbody><tr>' + tr.ignore('.alert-ignore').html() + '</tr></tbody>'

    swal({
      title     : 'Deleting Training Record',
      content   : alertText,
      text      : 'Are you sure you want to delete the training record?',
      icon      : 'warning',
      buttons   : {
        cancel : {
          text   : 'No, cancel',
          visible: true,
        },
        confirm: {
          text      : 'Yes, delete',
          closeModal: false
        }
      },
      dangerMode: true,
    })
      .then(willDelete => {
        if (willDelete) {
          $.ajax({
            url   : $.apiUrl() + '/v2/training/record/' + id,
            method: 'DELETE'
          }).success(() => {
            swal('Success!', 'The training record has been deleted.', 'success')
            $('.training-records-list').DataTable().row(tr).remove().draw()
            btn.parents('.modal').modal('hide')
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })
  })
  $('#tr-edit-delete').click(function () {
    let btn = $(this),
        id  = btn.data('id'),
        tr  = $('.delete-tr[data-id=' + id + ']').parents('tr')
    let alertText = document.createElement('table')
    alertText.className = 'table table-border'
    alertText.innerHTML = $('.training-records-list').first().children('thead').ignore('.alert-ignore').html()
      + '<tbody><tr>' + tr.ignore('.alert-ignore').html() + '</tr></tbody>'

    swal({
      title     : 'Deleting Training Record',
      content   : alertText,
      text      : 'Are you sure you want to delete the training record?',
      icon      : 'warning',
      buttons   : {
        cancel : {
          text   : 'No, cancel',
          visible: true,
        },
        confirm: {
          text      : 'Yes, delete',
          closeModal: false
        }
      },
      dangerMode: true,
    })
      .then(willDelete => {
        if (willDelete) {
          $.ajax({
            url   : $.apiUrl() + '/v2/training/record/' + id,
            method: 'DELETE'
          }).success(() => {
            swal('Success!', 'The training record has been deleted.', 'success')
            $('.training-records-list').DataTable().row(tr).remove().draw()
            btn.parents('.modal').modal('hide')
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })
  })
  $('#tr-view-edit').click(function () {
    let btn = $(this),
        id  = btn.data('id')
    $('.edit-tr[data-id=' + id + ']').trigger('click')
  })

  $('#e-training-submit').click(function (e) {
    e.preventDefault()
    let btn      = $(this),
        id       = btn.data('id'),
        formData = {
          position    : $('#e-training-position').val(),
          score       : $('#e-training-score').val(),
          session_date: $('#e-training-datetime').val(),
          duration    : $('#e-training-duration-hrs').val() + ':' + $('#e-training-duration-mins').val(),
          movements   : $('#e-training-movements').val(),
          location    : $('#e-training-location').val(),
          ots_status  : $('#e-training-ots-grp').find('input[name="ots_status"]:checked').val(),
          notes       : tinyMCE.get('e-training-notes').getContent()
        }

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)
    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'PUT',
      data  : formData
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      if (result.data.status === 'OK') {
        if ($('#e-training-ots-grp').find('input[name="ots_status"]:checked').val().match(/[12]/)) {
          btn.prop('disabled', true)
          $('#tr-edit-delete').prop('disabled', true)
          swal({
            title  : 'Success',
            text   : 'The training record has been successfully edited.',
            icon   : 'success',
            buttons: {
              toEval: {
                text     : 'Proceeed to OTS Evaluation',
                className: 'btn-success'
              },
              cancel: 'OK'
            }
          }).then(action => {
            if (action === 'toEval') {
              window.location.href = '/mgt/controller/' + $('#cid').val() + '/promote'
            } else return location.reload()
          })
        } else swal('Success!', 'The training record has been successfully edited. ', 'success')
      } else swal('Error!', 'Unable to edit training record. ' + (result.data.msg.match(/^Missing fields/) ? 'Missing fields.' : result.data.msg), 'error')
    }).fail((xhr, status, error) => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      swal('Error!', 'Unable to edit training record. ' + (xhr.responseJSON.data.msg.match(/^Missing fields/) ? 'Missing fields.' : xhr.responseJSON.data.msg), 'error')
    })
  })
  $('#n-training-submit').click(function (e) {
    e.preventDefault()
    let btn      = $(this),
        id       = btn.data('id'),
        formData = {
          position     : $('#n-training-position').val(),
          facility     : $('#fac').val(),
          score        : $('#n-training-score').val(),
          session_date : $('#n-training-datetime').val(),
          duration     : $('#n-training-duration-hrs').val() + ':' + $('#n-training-duration-mins').val(),
          movements    : $('#n-training-movements').val(),
          location     : $('#n-training-location').val(),
          instructor_id: $('#n-training-instructor').length ? $('#n-training-instructor').val() : 0,
          ots_status   : $('#n-training-ots-grp').find('input[name="ots_status"]:checked').val(),
          notes        : tinyMCE.get('n-training-notes').getContent()
        },
        cid      = $('#cid').val()

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)
    $.ajax({
      url   : $.apiUrl() + '/v2/user/' + cid + '/training/record',
      method: 'POST',
      data  : formData
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      if (result.data.status === 'OK') {
        if ($('#n-training-ots-grp').find('input[name="ots_status"]:checked').val().match(/[12]/)) {
          swal({
            title  : 'Success',
            text   : 'The training record has been successfully created.',
            icon   : 'success',
            buttons: {
              toEval: {
                text     : 'Proceeed to OTS Evaluation',
                className: 'btn-success'
              },
              cancel: 'OK'
            }
          }).then(action => {
            if (action === 'toEval')
              window.location.href = '/mgt/controller/' + $('#cid').val() + '/promote'
            else return location.reload()
          })
        } else swal('Success!', 'The training record has been successfully created. ', 'success').then(() => location.reload())
      } else swal('Error!', 'Unable to create training record. ' + (result.data.msg.match(/^Missing fields/) ? 'Missing fields.' : result.data.msg), 'error')
    }).fail((xhr, status, error) => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      swal('Error!', 'Unable to create training record. ' + (xhr.responseJSON.data.msg.match(/^Missing fields/) ? 'Missing fields.' : xhr.responseJSON.data.msg), 'error')
    })
  })

  $('#tng-artcc-select').change(function () {
    if ($(this).val()) $('#training-artcc-select-form').submit()
  })
  $('.training-duration[name="duration-mins"]').change(function () {
    let input = $(this),
        curr  = input.val(),
        modal = $(this).parents('div.modal')
    if (curr < 10 && $(this).val().length < 2) $(this).val('0' + curr)
    else if (curr > 59) $(this).val('59')
    if ($(this).val().length > 2) $(this).val($(this).val().substr(0, 2))
    if (modal.find('.training-duration[name="duration-hours"]').val() === '') modal.find('.training-duration[name="duration-hours"]').val('0')
  })

  $('.ots-status-input').change(function () {
    $('.ots-status-input').parent().attr('class', 'btn btn-default ots-status-input-label')
    $('.ots-exam-warning').hide()
    let parent = $(this).parent()
    switch (parseInt($(this).val())) {
      case 1:
        parent.removeClass('btn-default').addClass('btn-success')
        $('.ots-exam-warning').show()
        break
      case 2:
        parent.removeClass('btn-default').addClass('btn-danger')
        $('.ots-exam-warning').show()
        break
      case 3:
        parent.removeClass('btn-default').addClass('btn-info')
        break
    }
  })

  $('.add-new-record').click(function () {
    showTrainingRecordModal($(this))
  })
})

const showTrainingRecordModal = btn => {
  $('#n-training-datetime').datetimepicker({
    timepicker: true,
    format    : 'Y-m-d H:i',
    mask      : true,
    maxDate   : '+1970/01/02',
    step      : 15
  })
  tinymce.init({
    selector                     : '#n-training-notes',
    plugins                      : 'preview paste importcss searchreplace autolink autosave save visualblocks visualchars fullscreen image link media template table charmap hr nonbreaking toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap emoticons',
    imagetools_cors_hosts        : ['picsum.photos'],
    menubar                      : 'file edit view insert format tools table help',
    toolbar                      : 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap emoticons | fullscreen preview save | image media link',
    toolbar_sticky               : false,
    autosave_ask_before_unload   : false,
    autosave_interval            : '30s',
    autosave_prefix              : '{path}{query}-{id}-',
    autosave_restore_when_empty  : false,
    autosave_retention           : '2m',
    image_advtab                 : true,
    importcss_append             : true,
    height                       : 600,
    image_caption                : true,
    noneditable_noneditable_class: 'mceNonEditable',
    toolbar_mode                 : 'sliding',
    contextmenu                  : 'link image imagetools table',
  })
  $('.ots-exam-warning').hide();
  $('#new-training-record').modal('show')
}
