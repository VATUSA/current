var waitingDialog = waitingDialog || (function ($) {
  'use strict'

  // Creating modal dialog's DOM
  var $dialog = $(
    '<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
    '<div class="modal-dialog modal-m">' +
    '<div class="modal-content">' +
    '<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
    '<div class="modal-body">' +
    '<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
    '</div>' +
    '</div></div></div>')

  return {
    /**
     * Opens our dialog
     * @param message Custom message
     * @param options Custom options:
     *                  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
     *                  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
     */
    show: function (message, options) {
      // Assigning defaults
      if (typeof options === 'undefined') {
        options = {}
      }
      if (typeof message === 'undefined') {
        message = 'Loading'
      }
      var settings = $.extend({
        dialogSize  : 'm',
        progressType: 'ogblue',
        onHide      : null // This callback runs after the dialog was hidden
      }, options)

      // Configuring dialog
      $dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize)
      $dialog.find('.progress-bar').attr('class', 'progress-bar')
      if (settings.progressType) {
        $dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType)
      }
      $dialog.find('h3').text(message)
      // Adding callbacks
      if (typeof settings.onHide === 'function') {
        $dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
          settings.onHide.call($dialog)
        })
      }
      // Opening dialog
      $dialog.modal()
    },
    /**
     * Closes dialog
     */
    hide: function () {
      $dialog.modal('hide')
    }
  }

})(jQuery)
$.ajaxSetup({
  headers  : {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    'X-Requested-With': 'XMLHttpRequest'
  },
  xhrFields: {
    withCredentials: true
  },
})
$(function () {
  $('[data-toggle=\'tooltip\']').tooltip()
  $('[rel=\'tooltip\']').tooltip()
  $('.dropdown-menu')
    .mouseenter(function () {
      $(this).parent('li').addClass('active')
    })
    .mouseleave(function () {
      $(this).parent('li').removeClass('active')
    })
});
(function ($, window) {
  $.fn.replaceOptions = function (options) {
    var self, $option

    this.empty()
    self = this

    $.each(options, function (index, option) {
      $option = $('<option></option>')
        .attr('value', option.value)
        .text(option.text)
      self.append($option)
    })
  }
})(jQuery, window)
jQuery(document).ready(function ($) {
  $('.clickable-row').click(function () {
    window.location = $(this).data('href')
  })

  /** GDPR Compliance - Login Pop-up **/

 $('#login-link').click(function(e) {
   e.preventDefault();
   let redirect = $(this).data('action')
    if (Cookies.get('privacy-agree') === undefined) {
      //Show pop up
      let title = 'Privacy Policy Agreement'
      let content = `
    VATUSA'S Privacy Policy has been updated to reflect GDPR laws.
    By continuing to log in, and by using this service, you agree that you have read the 
    <a href="https://vatusa.net/info/privacy" target="_blank">Privacy Policy</a> 
    and understand the methods and usage of data collection contained therein.
    <br><br>
    <div class="checkbox">
    <label>
        <input class="checkbox" type="checkbox" id="privacy-agree"> I have read and understand VATUSA's Privacy Policy.
    </label>
    </div>
</label>
    `
      let dialog = bootbox.dialog({
        title    : title,
        message  : content,
        className: 'privacy-confirm',
        buttons  : {
          cancel: {
            label    : '<i class="fas fa-times"></i> I do not agree, cancel login',
            className: 'btn-danger',
            callback : function () {
              //
            }
          },
          ok    : {
            label    : '<i class="fas fa-sign-in-alt"></i> Continue login',
            className: 'btn-success continue-login',
            callback : function () {
              if ($('#privacy-agree').is(':checked')) {
                let parts = document.domain.split('.');
                Cookies.set('privacy-agree', true, {expires: 180, domain: '.vatusa.' + parts[parts.length - 1]})
                window.location = redirect + "?agreed"
              }
              else {
                //
              }
            }
          }
        }
      })
      /** Disable continue button, checkbox toggle **/
      $(document).on($.support.transition.end, '.modal.fade.in', function (e) {
        if ($(e.target).is('.privacy-confirm')) {
          $('.continue-login').attr('disabled', true)
          $('#privacy-agree').click(function () {
            if ($(this).is(':checked')) {
              $('.continue-login').attr('disabled', false)
            }
            else {
              $('.continue-login').attr('disabled', true)
            }
          })
        }
      })

    }
    else {
      //Already agreed, continue login
      window.location = redirect;
    }
  });
})