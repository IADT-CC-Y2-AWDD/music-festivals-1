<?php
require_once 'config.php';

if (is_logged_in()) {
  redirect("/home.php");
}

try {
  $request = new HttpRequest();
  $rules = [
    "email" => "present|email|minlength:7|maxlength:64",
    "password" => "present|minlength:8|maxlength:64",
    "name" => "present|minlength:2|maxlength:64"
  ];
  $request->validate($rules);
  $conn = null;

  if ($request->is_valid()) {
    $email = $request->input("email");
    $user = User::findByEmail($email);
    if ($user !== null) {
      $request->set_error("email", "Email address already registered");
    }
    else if ($user === null) {
      $password = $request->input("password");
      $name = $request->input("name");
      $new_user = new User();
      $new_user->email = $email;
      $new_user->password = password_hash($password, PASSWORD_DEFAULT);
      $new_user->name = $name;
      $new_user->save();
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
  $_SESSION['email'] = $new_user->email;
  $_SESSION['name'] = $new_user->name;
  redirect("/home.php");
}
else if (!$request->is_valid()) {
  require 'register-form.php';
}
?>
