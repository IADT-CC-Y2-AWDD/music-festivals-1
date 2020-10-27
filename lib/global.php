<?php
function redirect($url) {
  header("Location: ".APP_URL.$url);
  exit();
}
function is_logged_in() {
  return (isset($_SESSION) && isset($_SESSION['email']));
}
?>
