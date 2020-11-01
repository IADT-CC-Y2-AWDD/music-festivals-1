<?php
define('APP_URL', 'http://localhost/IADT-CC-Y2/register-login');

define('DB_SERVER', 'localhost');
define('DB_DATABASE', 'festivals');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

define('KEY_EXCEPTION', '__EXCEPTION__');

set_include_path(
  get_include_path() . PATH_SEPARATOR . dirname(__FILE__)
);

spl_autoload_register(function ($class_name) {
    require_once 'classes/' . $class_name . '.php';
});

session_start();

require_once "lib/global.php";
?>
