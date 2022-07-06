<?php
if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', 'hybrid_enqueue_script');

function hybrid_enqueue_script() {

	$version_script = '1';

	 //enqueue js
	 wp_enqueue_script('hybrid-custom-script', HYBRID_DIR_URL . 'assets/js/custom.js', ['jquery'], $version_script, true);
}
