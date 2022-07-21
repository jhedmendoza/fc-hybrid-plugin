<?php
if (!defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Users {

    public function __construct() {
      add_action('wp_ajax_create_candidate_account', [$this, 'create_candidate_account'] );
      add_action('wp_ajax_nopriv_create_candidate_account', [$this, 'create_candidate_account'] );

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

      $error = $this->validate_fields($firstname, $lastname, $username, $email, $password);

      if ($error['count'] > 0) {
        wp_json_encode(['status' => false, 'msg' => 'There are error(s) in the fields', 'errors' => $errors]);
        exit;
      }
     
      $user_id = wp_create_user($username, $password, $email);

      if ( !is_wp_error($user_id) ) {

          //user has been created
          $user = new WP_User($user_id);
          $user->set_role('candidate');

          add_user_meta( $user_id, 'fc_membership_level', $membership[1]);

          echo wp_json_encode(['status' => true, 'msg' => 'Account creation successful']);
          exit;
      } else {
          echo wp_json_encode(['status' => false, 'msg' => 'Something went wrong. Try again later.']);
          exit;
      }
      
    }

    public function validate_fields($firstname, $lastname, $username, $email, $password) {
      $errors = [];
      $counter = 0;
      if ( empty($firstname) )  {
        $errors['firstname']['required'] = 'First name is a required field.';
        $counter++;
      }
        
      if ( empty($lastname) )  {
        $errors['lastname']['required'] = 'Last name is a required field.';
        $counter++;
      }
    
      if ( empty($username) ) {
        $errors['username']['required'] = 'Username is a required field.';
        $counter++;
      }
      else if ( username_exists($username) ) {
        $errors['username']['exists'] = 'Username is already in use.';
        $counter++;
      }
        
      if ( empty($email) )  {
        $errors['email']['required'] = 'Email is a required field.';
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
        $errors['password']['required'] = 'Password is a required field.';
        $counter++;
      }

      $errors['count'] = $counter;

      return $errors;
      
    }


    
}

$users = new Users();
?>
