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
            btn.parents('tr').remove()
          })
            .error(err => swal('Error!', 'The training record could not be deleted.' + JSON.stringify(err), 'error'))
        } else {
          return false
        }
      })

  })
  $('.view-record').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    btn.html('<i class=\'fas fa-spinner fa-spin\'></i>').attr('disabled', true)

    $.ajax({
      url   : $.apiUrl() + '/v2/training/record/' + id,
      method: 'GET',
    }).done(result => {
      btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>').attr('disabled', false)
      console.log(result)

      $('.training-position').html(result.position)
      $('.training-student').html(result.student.fname + ' ' + result.student.lname)
      $('#training-artcc').html(result.facility.name);
      scoreStr = "";
      for(let i = 1; i <= 5; i++) {
        scoreStr += "<span class='glyphicon glyphicon-star";
        scoreStr += i > result.score ? "-empty" : ""
        scoreStr += "'></span> &nbsp;"
      }
      $('#training-score').html(scoreStr)
      $('#training-datetime').html(moment(result.session_date).format('dddd, MMMM Do YYYY, hh:mm'))
      //Duration

    })
      .fail((xhr, status, error) => {
        btn.html('<span class=\'glyphicon glyphicon-eye-open\'></span>').attr('disabled', false)
        alert('Unable to get training record. ' + error)
      })

    $('#view-training-record').modal('show')
  })
})