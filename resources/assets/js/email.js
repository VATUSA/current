import emailPage from './components/emails/emailPage'

$(document)
  .ready(() => {
    waitingDialog.show()
    $.ajax({
      url      : 'https://api.vatusa.net/v2/email',
//      url: 'http://api.vatusa.devel/v2/email',
      method   : 'get',
      dataType : 'json',
      xhrFields: {
        withCredentials: true
      }
    })
      .done((data) => {
        waitingDialog.hide()
        $('#emailRoot').html(emailPage(data))
      })
  })
  .on('click', '.btnSave', function () {
    if ($(this).data('type') === 'FULL') {
      if ($(`#password_${$(this).data('email').replace(/[@\-.]/g, '')}`).val() !== $(`#password2_${$(this).data('email').replace(/[@\-.]/g, '')}`).val()) {
        bootbox.alert('Password and Confirm Password fields do not match.')
        $(`#password_${$(this).data('email').replace(/[@\-.]/g, '')}`).focus()
        return false
      }
      $.ajax({
        url      : 'https://api.vatusa.net/v2/email',
        //url: 'http://api.vatusa.devel/v2/email',
        method   : 'put',
        xhrFields: {
          withCredentials: true
        },
        data     : {
          email   : $(this).data('email'),
          password: $(`#password_${$(this).data('email').replace(/[@\-.]/g, '')}`).val()
        }
      }).done(() => {
        bootbox.alert(`Password has been changed for ${$(this).data('email')} successfully.`)
      }).fail((data) => {
        if ($(`#password_${$(this).data('email').replace(/[@\-.]/g, '')}`).val().length < 6)
          bootbox.alert('<div class=\'alert alert-danger\'><strong><i class=\'fa fa-warning\'></i>Error!</strong> Your password must be at least 6 characters.')
        else bootbox.alert(`There was an error changing your password for ${$(this).data('email')}. Server response: ${data.responseText}. Please forward this information to the DSM if you need help.`)
      })
    } else {
      $.ajax({
        url      : 'https://api.vatusa.net/v2/email',
        //url: 'http://api.vatusa.devel/v2/email',
        method   : 'put',
        xhrFields: {
          withCredentials: true
        },
        data     : {
          email      : $(this).data('email'),
          destination: $(`#destination_${$(this).data('email').replace(/[@\-.]/g, '')}`).val(),
          static     : ($(`#static_${$(this).data('email').replace(/[@\-.]/g, '')}`).val() === 'Yes') ? true : false
        }
      }).done(() => {
        bootbox.alert(`Destination for ${$(this).data('email')} has been set successfully.`)
      }).fail((data) => {
        bootbox.alert(`There was an error changing the destination for ${$(this).data('email')}.  Server response: ${data.responseText}. Please forward this information to the DSM`)
      })
    }
  })
