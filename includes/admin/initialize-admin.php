<?php
if ( !defined('ABSPATH') ) exit; // Exit if accessed directly

add_action( 'admin_menu', 'initialize_fc_logo_manager_menus' );

function initialize_fc_logo_manager_menus() {
  
  $title = 'Logo Manager';
  $slug  = 'logo-manager';
  $icon  = 'dashicons-images-alt2';
  $position = 100;

  add_menu_page( $title, $title, 'manage_options', $slug, '', $icon, $position );

  $logo_list_menu  = add_submenu_page( $slug, 'Logo List', 'Logo List', 'manage_options', 'fc-logo-list', 'fc_logo_manager_list');
  $logo_add_menu = add_submenu_page( $slug, 'Add Logo', 'Add Logo', 'manage_options', 'fc-logo-add', 'fc_logo_manager_add');

  remove_submenu_page($slug, $slug);

  add_action('load-'.$logo_list_menu, 'load_admin_css_js');
  add_action('load-'.$logo_add_menu, 'load_admin_css_js');
 
}

function load_admin_css_js() {
  add_action( 'admin_enqueue_scripts', 'enqueue_admin_css_js');
}

function enqueue_admin_css_js() {

  $admin_version_script = '1';

  //Core media script
  wp_enqueue_media();

  wp_enqueue_style('bootstrap-admin', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css', [], '5.2.0');
  wp_enqueue_style('fc-admin-page', HYBRID_DIR_URL . 'includes/admin/assets/fc-admin-logo-manager.css', [], $admin_version_script);


  wp_enqueue_script('popper','https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js', [], '2.11.5', true );
  wp_enqueue_script('bootstrap-admin','https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js', ['popper'], '5.2.0', true );
  wp_enqueue_script('fc-admin-page', HYBRID_DIR_URL . 'includes/admin/assets/fc-admin-logo-manager.js', [], $admin_version_script, true );
}

// function get_contacts() {
//   global $wpdb;
//   $table_contacts = $wpdb->prefix . 'nmb_contact_details';
//   $query = "SELECT * FROM $table_contacts ORDER BY date_created DESC";
//   $results =  $wpdb->get_results($query);
//   return $results;
// }

function fc_logo_manager_add() {
  hybrid_include('includes/admin/logo_manager/add.php');
}

function fc_logo_manager_list() {
  hybrid_include('includes/admin/logo_manager/list.php');
}
