<?php
require_once 'lib/validation-generic.php';

function validate_email($email) {
  global $errors;
  if (!is_present($email)) {
    $errors['email'] = "Email required";
  }
  else if (!is_safe_email($email)) {
    $errors['email'] = "Email contains unsafe characters";
  }
  else if (!is_valid_email($email)) {
    $errors['email'] = "Email format is not valid";
  }
  else if (!has_length($email, ["min" => 7, "max" => 64])) {
    $errors['email'] = "Email is too short or long";
  }
}
function validate_password($password) {
  global $errors;
  if (!is_present($password)) {
    $errors['password'] = "Password required";
  }
  else if (!has_length($password, ["min" => 8, "max" => 64])) {
    $errors['password'] = "Password is too short or long";
  }
}
?>
