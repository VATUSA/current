$(function () {
  $('#pos-types li a').click(function (e) {
    e.preventDefault()
    let target = $(this).data('controls')
    $('#training-content div[role="tabpanel"]#' + target).show()
    $('#training-content div[role="tabpanel"]:not(#' + target + ')').hide()
  })
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

  $('.delete-tr').click(function () {
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
            tr.remove()
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })

  })
  $('.view-tr').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)

    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'GET',
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>').attr('disabled', false)
      console.log(result)

      $('#view-training-record .tr-modal-delete').attr('data-id', id)
      $('#tr-view-edit').attr('data-id', id)
      $('.training-position').html(result.position)
      $('.training-student').html(result.student.fname + ' ' + result.student.lname)
      $('#training-artcc').html(result.facility.name)
      scoreStr = ''
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
      $('#training-instructor').html(result.instructor.fname + ' ' + result.instructor.lname)
      $('#training-notes').html(result.notes)

      $('#view-training-record').modal('show')
    })
      .fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>').attr('disabled', false)
        swal('Error!', 'Unable to get training record. ' + error, 'error')
      })
  })
  $('.edit-tr').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)

    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'GET',
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-pencil\'></span>').attr('disabled', false)

      $('#edit-training-record .tr-modal-delete, #e-training-submit').attr('data-id', id)
      $('input.e-training-position').val(result.position)
      $('span.e-training-position').html(result.position)
      $('#e-training-student').html(result.student.fname + ' ' + result.student.lname)
      $('#e-training-artcc').html(result.facility.name)
      $('#e-training-score').val(result.score)
      $('#e-training-datetime').val(moment(result.session_date).format('YYYY-MM-DD HH:mm'))
      $('#e-training-duration-hrs').val(moment.duration(result.duration).hours())
      $('#e-training-duration-mins').val(moment.duration(result.duration).minutes())
      $('#e-training-movements').val(result.movements)
      $('#e-training-location').val(result.location)
      $('#e-training-instructor').html(result.instructor.fname + ' ' + result.instructor.lname)
      // $('#e-training-notes').html(result.notes)

      $('#e-training-datetime').datetimepicker({
        timepicker: true,
        format    : 'Y-m-d H:i',
        mask      : true,
        maxDate   : '+1970/01/01',
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
      $('#edit-training-record').modal('show')

    })
      .fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-pencil\'></span>').attr('disabled', false)
        swal('Error!', 'Unable to get training record. ' + error, 'error')
      })

  })
  $('#edit-training-record').on('hidden.bs.modal', function () {
    tinymce.get('e-training-notes').remove()
  })

  $('.tr-modal-delete').click(function () {
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
            tr.remove()
            btn.parents('.modal').modal('hide')
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })
  })

  $('#e-training-submit').click(function (e) {
    e.preventDefault()
    let btn      = $(this),
        id       = btn.data('id'),
        formData = {
          position    : $('#e-training-position').val(),
          progress    : $('#e-training-score').val(),
          session_date: $('#e-training-datetime').val(),
          duration    : $('#e-training-duration-hrs').val() + ':' + $('#e-training-duration-mins').val(),
          movements   : $('#e-training-movements').val(),
          location    : $('#e-training-location').val(),
          notes       : tinyMCE.get('e-training-notes').getContent()
        }

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)
    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'PUT',
      data  : formData
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      if (result.status === 'OK')
        swal('Success!', 'The training record has been successfully edited. ', 'success')
      else
        swal('Error!', 'Unable to edit training record. ' + result.error, 'error')
    }).fail((xhr, status, error) => {
      btn.html('<span class=\'glyphicon glyphicon-ok\'></span> Submit').attr('disabled', false)
      swal('Error!', 'Unable to edit training record. ' + error, 'error')
    })
  })

})