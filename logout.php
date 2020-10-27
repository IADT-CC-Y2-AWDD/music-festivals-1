<?php
require_once 'config.php';

if (!is_logged_in()) {
  redirect("/login-form.php");
}

unset($_SESSION['email']);
unset($_SESSION['name']);

redirect("/index.php");
?>
