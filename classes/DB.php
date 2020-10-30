<?php
class DB {
  private static $instance = null;
  private $conn;

  private $server = DB_SERVER;
  private $database = DB_DATABASE;
  private $username = DB_USERNAME;
  private $password = DB_PASSWORD;

  private function __construct() {
    $dsn = "mysql:host={$this->server};dbname={$this->database}";
    $this->conn = new PDO($dsn, $this->username,$this->password);
    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new DB();
    }
    return self::$instance;
  }

  public function getConnection() {
    return $this->conn;
  }
}
?>