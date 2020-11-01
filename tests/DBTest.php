<?php declare(strict_types=1);

require_once "config.php";

use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase {

  protected function setUp() : void {
  }

  public function testOpenClose() : void {
    $db = new DB();
    $this->assertFalse($db->is_open());
    $conn = $db->open();
    $this->assertTrue($db->is_open());
    $conn = $db->close();
    $this->assertFalse($db->is_open());
  }

  protected function tearDown() : void {
  }
}
