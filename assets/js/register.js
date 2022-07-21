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

      var membershipLevel = $(this).parents('.candidate-sign-up').attr('id');

      console.log(membershipLevel);

      var data = {
        'action'   : 'create_candidate_account',
        'firstname': firstName,
        'lastname' : lastName,
        'username' : username,
        'email'    : email,
        'password' : password,
        'membership_level' : membershipLevel
      };

      xhr = $.ajax({
        url    : fc_ajax_url,
        data   : data,
        method : 'POST',
        beforeSend: function() {},
        complete: function() {},
        success: function(result) {
          var resp = JSON.parse(result);
          console.log(resp);
        },
      });

    });
  }


  $(document).ready(init);

  }(jQuery));
