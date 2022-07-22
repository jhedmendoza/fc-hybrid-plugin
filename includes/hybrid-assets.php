<?php
if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

add_action('wp_head', 'js_inline_script' );
add_action('wp_enqueue_scripts', 'hybrid_enqueue_script');

function hybrid_enqueue_script() {

	$version_script = '1.2';

	wp_enqueue_style('hybrid-pdf-layout', HYBRID_DIR_URL . 'assets/css/pdf-layout.css', [], $version_script);
	wp_enqueue_style('hybrid-custom', HYBRID_DIR_URL . 'assets/css/custom.css', [], $version_script);

	 //enqueue js
	 wp_enqueue_script('hybrid-pdf-script', HYBRID_DIR_URL . 'assets/js/pdf.js', ['jquery'], $version_script, true);
	 wp_enqueue_script('hybrid-register-script', HYBRID_DIR_URL . 'assets/js/register.js', ['jquery'], $version_script, true);
}

function js_inline_script() {
?>
<script type="text/javascript">
   		var fc_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
		var siteurl = "<?php echo site_url(); ?>";
</script>
<?php
}
