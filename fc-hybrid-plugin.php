<?php
/*
Plugin Name: FC Hybrid Custom Plugin
Description: This plugin is used for handling and extending wp job manager form fields, payment gateway integration and CV generation etc.
Version: 1.0
Author: Hybrid Anchor
Author URI: https://www.hybridanchor.com/
*/

if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

if ( !class_exists('Hybrid') ) :

class Hybrid {

	/** @var string The plugin version number. */

	var $version = '1';


	function __construct() {
		register_activation_hook( __FILE__  , array($this, 'hybrid_install') );
	}

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
		require_once(HYBRID_PATH.'includes/controllers/Users.php');

		//Include core.
		hybrid_include('includes/hybrid-assets.php');
 	}


	function define( $name, $value = true ) {

		if( !defined($name) ) {
			define( $name, $value );
		}
	}
	

	function hybrid_install() {

		global $wpdb;
		$table_membership_level = $wpdb->prefix . 'membership_level';
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql[] = "CREATE TABLE $table_membership_level (
						id INT (11) AUTO_INCREMENT,
						membership_name VARCHAR(100),
						membership_code VARCHAR(100),
						PRIMARY KEY (id)
					) $charset_collate";
	
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql) ;
	
		$membership_level_data = include 'includes/config/database/membership_level.php';

		$check_membership_level = $wpdb->get_var( "SELECT COUNT(*) FROM $table_membership_level" );

		if ($check_membership_level > 0)
			return false;
		
		foreach ($membership_level_data as $level) {
			$wpdb->insert($table_membership_level, array(
				'membership_name'  => $level['name'],
				'membership_code'  => $level['code'],
			));
		}
	
		add_option('hybrid_db_version', HYBRID_VERSION);
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