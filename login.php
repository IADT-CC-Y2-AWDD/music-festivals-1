<?php
require_once 'config.php';
require_once 'lib/validation-generic.php';
require_once 'lib/validation-bespoke.php';

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

  // if there were no validation errors, check the username and password
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
    // if select query returned no rows, add an error message to the errors array
    if ($select_stmt->rowCount() === 0) {
      $errors['email'] = "Email address/password invalid";
    }
    // if select query returned at least one row, then
    //  - check the password against the password from the first returned row
    //  - if the passwords do not match, add an error message to the errors array
    else if ($select_stmt->rowCount() !== 0) {
      $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
      $name = $row['name'];
      $password_hash = $row['password'];
      if (!password_verify($password, $password_hash)) {
        $errors['email'] = "Email address/password invalid";
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
  // close the connection
  $conn = null;
}

// if there are no errors then
if (empty($errors)) {
  // log the user in by storing their email and name in the session array
  $_SESSION['email'] = $email;
  $_SESSION['name'] = $name;
  // redirect the user to their home page
  redirect("/home.php");
}
// else if there are errors then
else if (!empty($errors)) {
  // display the login form again with submitted data and error messages
  require 'login-form.php';
}
?>
