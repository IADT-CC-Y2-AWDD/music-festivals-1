<?php
class DB {
  private static $instance = null;

  private $server = DB_SERVER;
  private $database = DB_DATABASE;
  private $username = DB_USERNAME;
  private $password = DB_PASSWORD;

  private $conn;
  private $dsn;

  private function __construct() {
    $this->dsn = "mysql:host={$this->server};dbname={$this->database}";
    $this->conn = null;
  }

  public static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new DB();
    }
    return self::$instance;
  }

  public function open() {
    if ($this->conn === null) {
      $this->conn = new PDO($this->dsn, $this->username,$this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $this->conn;
  }

  public function close() {
    $this->conn = null;
  }
}
?>
