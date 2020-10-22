<?php
require_once 'config.php';
require_once 'lib/validation-errors.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === "GET") {
  // retrieving the form so it can be completed and submitted
}
else if ($method === "POST") {
  // the form was submitted but there are errors
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Form validation example</title>

    <link href="<?= APP_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= APP_URL ?>/assets/css/template.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/form.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <?php require 'include/header.php'; ?>
      <?php require 'include/navbar.php'; ?>
      <main role="main">
        <h1>Login Form</h1>
        <form name='login' action="login.php" method="post">

          <div class="form-field">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" value="<?= get_value('email') ?>" />
            <span class="error"><?= get_error('email') ?></span>
          </div>

          <div class="form-field">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" />
            <span class="error"><?= get_error('password') ?></span>
          </div>

          <div class="form-field">
            <label></label>
            <input type="submit" name="submit" value="Submit" />
          </div>

        </form>
      </main>
      <?php require 'include/footer.php'; ?>
    </div>
    <script src="<?= APP_URL ?>/assets/js/jquery-3.5.1.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
