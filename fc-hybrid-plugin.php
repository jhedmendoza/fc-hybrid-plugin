<?php
/*
Plugin Name: FC Hybrid Custom Plugin
Description: This plugin is used for generating pdf using user's CV details.
Version: 1.0
Author: Hybrid Anchor
Author URI: https://www.hybridanchor.com/
*/

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

if ( !class_exists('Hybrid') ) :

class Hybrid {

	/** @var string The plugin version number. */

	var $version = '1';


	function __construct() {}

	function initialize() {

		switch ($_SERVER['SERVER_NAME']) {

			case 'football-careers.local':
				$this->define('ENV', 'local');
			break;

			case 'dev-football.hybridanchor.com':
				$this->define('ENV', 'staging');
			break;

			default:
				$this->define('ENV', 'prod');
			break;
		}

		// Define constants.
		$this->define('HYBRID_PATH', plugin_dir_path( __FILE__ ) );
		$this->define('HYBRID_DIR_URL', plugin_dir_url( __FILE__ ) );
		$this->define('HYBRID_BASENAME', plugin_basename( __FILE__ ) );
		$this->define('HYBRID_VERSION', $this->version );

		//Include libraries
		require_once(HYBRID_PATH.'includes/lib/vendor/autoload.php');

		// Include utility functions.
		require_once(HYBRID_PATH.'includes/utility-function.php');

		//Include controllers.
    require_once(HYBRID_PATH.'includes/controllers/FCPDF.php');

		//Include core.
		hybrid_include('includes/hybrid-assets.php');
 	}


	function define( $name, $value = true ) {

		if( !defined($name) ) {
			define( $name, $value );
		}
	}

}


function hybrid() {

	global $hybrid;

	// Instantiate only once.
	if( !isset($hybrid) ) {
		$hybrid = new Hybrid();
		$hybrid->initialize();
	}

	return $hybrid;

 }

 hybrid();

endif; // class_exists check
