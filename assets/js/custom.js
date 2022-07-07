(function ($) {

  function init() {

    $('table.resume-manager-resumes tbody tr').each(function(i, e) {
        console.log('el', e);
        var url = $(e).find('.action .candidate-dashboard-action-edit').attr('href');
        var urlParams = new URLSearchParams(url);
        var resumeID = urlParams.get('resume_id');
        console.log(resumeID);
        $(e).find('.action').append('<a target="_blank" href="'+fc.site_url+'/wp-content/uploads/fc_hybrid_plugin/'+resumeID+'.pdf" class="dl-resume-pdf"><i class="fa fa-file-pdf-o"></i>Download Resume</a>');
    });

  }


  $(document).ready(init);

  }(jQuery));
