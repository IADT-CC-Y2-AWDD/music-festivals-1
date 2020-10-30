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
    "email",      "password"
  ];

  $post_params = get_post_params($allowed_params);
  $errors = [];

  validate_email($post_params['email']);
  validate_password($post_params['password']);

  $email = $post_params['email'];
  $password = $post_params['password'];
  $conn = null;

  if (empty($errors)) {
    $user = User::findByEmail($email);
    if ($user === null) {
      $errors['email'] = "Email address/password invalid";
    }
    else if ($user !== null) {
      if (!password_verify($password, $user->password)) {
        $errors['email'] = "Email address/password invalid";
      }
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
  $_SESSION['email'] = $user->email;
  $_SESSION['name'] = $user->name;
  redirect("/home.php");
}
else if (!empty($errors)) {
  require 'login-form.php';
}
?>
