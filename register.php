<?php
require_once 'config.php';
require_once 'lib/validation-generic.php';
require_once 'lib/validation-bespoke.php';
require_once 'classes/User.php';

if (is_logged_in()) {
  redirect("/home.php");
}

try {
  $allowed_params = [
    "email",      "password",   "name"
  ];

  $post_params = get_post_params($allowed_params);
  $errors = [];

  validate_email($post_params['email']);
  validate_password($post_params['password']);
  validate_name($post_params['name']);

  $email = $post_params['email'];
  $password = $post_params['password'];
  $name = $post_params['name'];
  $conn = null;

  if (empty($errors)) {
    $user = User::findByEmail($email);
    if ($user !== null) {
      $errors['email'] = "Email address already registered";
    }
    else if ($user === null) {
      $new_user = new User();
      $new_user->email = $email;
      $new_user->password = password_hash($password, PASSWORD_DEFAULT);
      $new_user->name = $name;
      $new_user->save();
    }
  }
}
catch(PDOException $e) {
  $errors[KEY_EXCEPTION] = "Database exception: " . $e->getMessage();
}
catch(Exception $e) {
  $errors[KEY_EXCEPTION] = "Exception: " . $e->getMessage();
}

if (empty($errors)) {
  $_SESSION['email'] = $new_user->email;
  $_SESSION['name'] = $new_user->name;
  redirect("/home.php");
}
else if (!empty($errors)) {
  require 'register-form.php';
}
?>
