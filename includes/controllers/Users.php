<?php
if (!defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Users {

    public function __construct() {

      session_start();

      add_action('template_redirect', [$this, 'candidate_checkout_details']);
      add_action('wp_ajax_create_candidate_account', [$this, 'create_candidate_account'] );
      add_action('wp_ajax_nopriv_create_candidate_account', [$this, 'create_candidate_account'] );
      add_filter( 'woocommerce_checkout_fields', [$this, 'prefilled_checkout_form'] );
    }

    public function candidate_checkout_details() {

      if ( is_wc_endpoint_url('order-received') ) {
        global $wp;

        $order_id = intval( str_replace( 'checkout/order-received/', '', $wp->request ) );

        if ($order_id) 
        {
          $user_query = new WP_User_Query( [
            'search' => '*'.$_SESSION['username'].'*',
            'search_columns' => array('user_login'),
          ]);
         
          $user_id = $user_query->get_results()[0]->data->ID;

          //update role to candidate
          $user = new WP_User($user_id);
          $user->set_role('candidate');
        }

      }

    }

    public function add_to_cart_candidate_package($membership_type) {
      global $woocommerce;

      $product_id = 0;

      //we only allow one item in the cart at a time.
      $woocommerce->cart->empty_cart();

      $args = array(
        'category' => array('candidate'),
      );

      $products = wc_get_products($args);

      foreach ($products as $product) {
        if ($product->get_attribute('package_type') == $membership_type) {
          $product_id = $product->id;
          break;
        }
      }

      $woocommerce->cart->add_to_cart($product_id);

      echo wp_json_encode([
        'status'  => true,
        'errors'  => 0,
        'checkout'=> true,
        'msg'     => 'Items successfully added to cart.',
      ]);
      exit;
    }

    public function create_candidate_account() 
    {
      $firstname= sanitize_text_field($_POST['firstname']);
      $lastname = sanitize_text_field($_POST['lastname']);
      $username = sanitize_text_field($_POST['username']);
      $email    = sanitize_text_field($_POST['email']);
      $password = sanitize_text_field($_POST['password']);

      $membership_level = sanitize_text_field($_POST['membership_level']);
      $membership = explode('-', $membership_level);
      $membership_type = isset($membership[1]) ? $membership[1] : '';

      $error = $this->validate_fields($firstname, $lastname, $username, $email, $password);

      if ($error['count'] > 0) {
        echo wp_json_encode(['status' => true, 'msg' => 'There are error(s) in the fields', 'errors' => $error]);
        exit;
      }

      if ($membership_type == 'free') {
        $user_id = wp_create_user($username, $password, $email);
      }
      else {

        $_SESSION['firstname']= $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['username'] = $username;
        $_SESSION['email']    = $email;
        $_SESSION['password'] = $password;
        //add premium package to cart and redirect them to checkout page
        $this->add_to_cart_candidate_package($membership_type);
      }

      if ( !is_wp_error($user_id) ) {

          //user has been created
          $user = new WP_User($user_id);
          $user->set_role('candidate');

          //attach membership level on candidate depending on chosen package
          add_user_meta( $user_id, 'fc_membership_level', $membership_type);

          echo wp_json_encode(['status' => true, 'msg' => 'Account creation successful', 'errors' => 0,'checkout' => false]);
      } 
      else 
        echo wp_json_encode(['status' => false, 'msg' => 'Something went wrong. Try again later.']);

      exit;
    }

    public function validate_fields($firstname, $lastname, $username, $email, $password) {
      $errors = [];
      $counter = 0;
      if ( empty($firstname) )  {
        $errors['firstname']['required'] = 'First name is required.';
        $counter++;
      }
        
      if ( empty($lastname) )  {
        $errors['lastname']['required'] = 'Last name is required.';
        $counter++;
      }
    
      if ( empty($username) ) {
        $errors['username']['required'] = 'Username is required.';
        $counter++;
      }
      else if ( username_exists($username) ) {
        $errors['username']['exists'] = 'Username is already in use.';
        $counter++;
      }
        
      if ( empty($email) )  {
        $errors['email']['required'] = 'Email is required.';
        $counter++;
      }
      else if ( !is_email($email) )  {
        $errors['email']['not_valid'] = 'This is not a valid email address';
        $counter++;
      }
      else if ( email_exists($email) )  {
        $errors['email']['exists'] = 'Email is already in use.';
        $counter++;
      }

      if ( empty($password) )  {
        $errors['password']['required'] = 'Password is required.';
        $counter++;
      }

      $errors['count'] = $counter;

      return $errors;
      
    }
    
    public function prefilled_checkout_form( $fields ) {
      global $woocommerce;

      if ( !is_null($_SESSION['firstname']) ) {
        $fields['billing']['billing_first_name']['default'] = $_SESSION['firstname'];
      }

      if ( !is_null($_SESSION['lastname']) ) {
        $fields['billing']['billing_last_name']['default'] = $_SESSION['lastname'];
      }

      if ( !is_null($_SESSION['email']) ) {
        $fields['billing']['billing_email']['default'] = $_SESSION['email'];
      }

      if ( !is_null($_SESSION['email']) ) {
        $fields['account']['account_username']['default'] = $_SESSION['username'];
      }

      return $fields;
    }
  
}

$users = new Users();
?>
