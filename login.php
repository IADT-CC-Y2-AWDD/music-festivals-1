<?php
require_once 'config.php';
require_once 'lib/validation-generic.php';
require_once 'lib/validation-bespoke.php';

if (is_logged_in()) {
  redirect("/home.php");
}

try {
  $request = new HttpRequest();
  $request->initialise();
  $rules = [
    "email" => "present|email|minlength:7|maxlength:64",
    "password" => "present|minlength:8|maxlength:64"
  ];
  $request->validate($rules);
  $conn = null;

  if ($request->is_valid()) {
    $email = $request->input("email");
    $user = User::findByEmail($email);
    if ($user === null) {
      $request->set_error("email", "Email address/password invalid");
    }
    else if ($user !== null) {
      $password = $request->input("password");
      if (!password_verify($password, $user->password)) {
        $request->set_error("email", "Email address/password invalid");
      }
    }
  }
}
catch(PDOException $e) {
  $request->set_exception("Database exception: " . $e->getMessage());
}
catch(Exception $e) {
  $request->set_exception("Exception: " . $e->getMessage());
}

if ($request->is_valid()) {
  $_SESSION['email'] = $user->email;
  $_SESSION['name'] = $user->name;
  redirect("/home.php");
}
else if (!$request->is_valid()) {
  require 'login-form.php';
}
?>
