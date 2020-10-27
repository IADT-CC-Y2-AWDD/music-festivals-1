<?php
require_once 'config.php';
require_once 'lib/validation-generic.php';
require_once 'lib/validation-bespoke.php';

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

  // if there are no validdation errors, check if the user's email adress is already registered
  //  - if it is, add an error message to the $errors array
  //  - otherwise add the user's details to the database
  if (empty($errors)) {
    // connect to the database
    $conn = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // execute a select statement to see if the email address is in the database
    $select_sql = "SELECT * FROM users WHERE email = :email";
    $select_params = [
      ":email" => $email
    ];
    $select_stmt = $conn->prepare($select_sql);
    $select_status = $select_stmt->execute($select_params);
    // if there was an error executing the select query, throw an exception
    if (!$select_status) {
      $error_info = $select_stmt->errorInfo();
      $message = "SQLSTATE error code = ".$error_info[0]."; error message = ".$error_info[2];
      throw new Exception("Database error executing database select query: " . $message);
    }
    // if select query returned at least one row, add an error message to the $errors array
    if ($select_stmt->rowCount() !== 0) {
      $errors['email'] = "Email address already registered";
    }
    // otherwise if select query returned no rows, add the user's details to the database
    else if ($select_stmt->rowCount() === 0) {
      // execute a query to insert the user's details into the database
      $insert_sql = "INSERT INTO users (email, password, name) VALUES (:email, :password, :name)";
      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $insert_params = [
        ":email" => $email,
        ":password" => $password_hash,
        ":name" => $name
      ];
      $insert_stmt = $conn->prepare($insert_sql);
      $insert_status = $insert_stmt->execute($insert_params);
      // if there was an error executing the insert query, throw an exception
      if (!$insert_status) {
        $error_info = $insert_stmt->errorInfo();
        $message = "SQLSTATE error code = ".$error_info[0]."; error message = ".$error_info[2];
        throw new Exception("Database error executing database insert query: " . $message);
      }
      // if insert query did not insert exactly one row, throw an exception
      if ($insert_stmt->rowCount() !== 1) {
        throw new Exception("Failed to insert new user.");
      }
    }
  }
}
// if an exception is caught then store an message in the errors array using the exception key
catch(PDOException $e) {
  $errors[KEY_EXCEPTION] = "Database exception: " . $e->getMessage();
}
catch(Exception $e) {
  $errors[KEY_EXCEPTION] = "Exception: " . $e->getMessage();
}
// whether an exception occurred or not
finally {
  // close the database connection
  $conn = null;
}

// if there were no errors then
if (empty($errors)) {
  // log the user in by storing their email and name in the session array
  $_SESSION['email'] = $email;
  $_SESSION['name'] = $name;
  // redirect the user to their home page
  redirect("/home.php");
}
// else if there were errors then
else if (!empty($errors)) {
  // display the register form again with submitted data and error messages
  require 'register-form.php';
}
?>
