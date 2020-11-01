<?php declare(strict_types=1);

require_once "config.php";
require_once 'lib/validation-bespoke.php';

use PHPUnit\Framework\TestCase;

$errors = [];

final class ValidationBespokeTest extends TestCase {

  protected function setUp() : void {
  }

  public function testValidateEmail() : void {
    global $errors;
    validate_email("");
    $this->assertArrayHasKey("email", $errors);
    $errors = [];
    validate_email("fred(@)bloggs.com");
    $this->assertArrayHasKey("email", $errors);
    $errors = [];
    validate_email("fredatbloggs.com");
    $this->assertArrayHasKey("email", $errors);
    $errors = [];
    validate_email("a@b.co");
    $this->assertArrayHasKey("email", $errors);
    $errors = [];
    validate_email("fred@bloggs.com");
    $this->assertArrayNotHasKey("email", $errors);
    $errors = [];
  }

  public function testValidatePassword() : void {
    global $errors;
    validate_password("");
    $this->assertArrayHasKey("password", $errors);
    $errors = [];
    validate_password("abc");
    $this->assertArrayHasKey("password", $errors);
    $errors = [];
    validate_password("abcdefgh");
    $this->assertArrayNotHasKey("password", $errors);
    $errors = [];
  }

  public function testValidateName() : void {
    global $errors;
    validate_name("");
    $this->assertArrayHasKey("name", $errors);
    $errors = [];
    validate_name("a");
    $this->assertArrayHasKey("name", $errors);
    $errors = [];
    validate_name("ab");
    $this->assertArrayNotHasKey("name", $errors);
    $errors = [];
  }

  protected function tearDown() : void {
  }
}
