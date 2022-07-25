(function ($) {

  var xhr;

  function init() {
    $('.candidate-sign-up a.elementor-button').on('click', function(e) {
      e.preventDefault();

      var firstName = $('#signup-firstname').val().trim();
      var lastName  = $('#signup-lastname').val().trim();
      var username  = $('#signup-username').val().trim();
      var email     = $('#signup-email').val().trim();
      var password  = $('#signup-password').val().trim();

      var _that = $('.candidate-sign-up a.elementor-button');

      var membershipLevel = $(this).parents('.candidate-sign-up').attr('id');

      var packageBtn = $('#'+membershipLevel+ ' a.elementor-button');

      var data = {
        'action'   : 'create_candidate_account',
        'firstname': firstName,
        'lastname' : lastName,
        'username' : username,
        'email'    : email,
        'password' : password,
        'membership_level' : membershipLevel
      };

      console.log(data);

      //reset error when form submitted again.
      $('._error').remove();

      xhr = $.ajax({
        url    : fc_ajax_url,
        data   : data,
        method : 'POST',
        beforeSend: function() {
          _that.css('pointer-events', 'none');
          packageBtn.after('<p class="preloader">Processing. Please wait.</p>');
          packageBtn.hide();
        },
        complete: function() {
          _that.css('pointer-events', 'all');
          $('.preloader').remove();
         packageBtn.show();
        },
        success: function(result) {
          var resp = JSON.parse(result);

          if (resp.status) {

            if (resp.errors == 0) {

              if (resp.checkout) 
                location.replace(siteurl+'/checkout?user_type=candidate');
              else {
                alert("You have successfully subscribe to our site.");
                location.replace(siteurl+'/my-profile');
              }  
            
            }
            else {
              $.each(resp.errors, function(ele, val) {

                 var _element = $('#signup-'+ele);

                 if (val.required) 
                  _element.after('<p class="_error required">'+val.required+'<p/>');
                                 
                 if (val.not_valid) 
                  _element.after('<p class="_error required">'+val.not_valid+'<p/>');

                  if (val.exists) 
                  _element.after('<p class="_error required">'+val.exists+'<p/>');

              });

             
                $('html, body').animate({
                  scrollTop: ($('._error').offset().top - 300)
                }, 500);
              
            }

          }
        },
      });

    });
  }


  $(document).ready(init);

  }(jQuery));
