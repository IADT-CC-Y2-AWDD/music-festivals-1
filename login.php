<?php
require_once 'config.php';
require_once 'lib/validation-generic.php';
require_once 'lib/validation-bespoke.php';

$allowed_params = [
  "email",      "password",   "name"
];

$post_params = get_post_params($allowed_params);
$errors = [];

validate_email($post_params['email']);
validate_password($post_params['password']);

if (empty($errors)) {
  header("Location: home.php");
}
else {
  require 'login-form.php';
}
?>
