(function ($) {

  function init() {

    var mediaUploader;

    $('.upload-image').click(function(e) {
      e.preventDefault();
      // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      // Extend the wp.media object
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
        text: 'Choose Image'
      }, multiple: false });
  
      // When a file is selected, grab the URL and set it as the text field's value
      mediaUploader.on('select', function() {
        attachment = mediaUploader.state().get('selection').first().toJSON();
        $('.logo-thumbnail').attr('src', attachment.url);
        $('.upload-image').text('Update logo');
      });
      // Open the uploader dialog
      mediaUploader.open();
    });

  }

$(document).ready(init);

}(jQuery));
