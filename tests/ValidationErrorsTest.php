<?php declare(strict_types=1);

require_once "config.php";
require_once 'lib/validation-errors.php';

use PHPUnit\Framework\TestCase;

$post_params = [];
$errors = [];

final class ValidationErrorsTest extends TestCase {

  protected function setUp() : void {
    global $post_params;
    global $errors;

    $post_params = [
      "email" => "fred@bloggs.com",
      "languages" => ["ga", "en", "es"],
      "county" => "wicklow",
      "newsletter" => "on"
    ];

    $errors = [
      "email" => "Email required"
    ];
  }

  public function testGetValue() : void {
    global $post_params;
    $this->assertEquals(get_value("email"), $post_params["email"]);
    $this->assertIsArray(get_value("languages"));
    $this->assertEquals(get_value("languages")[0], "ga");
    $this->assertEquals(get_value("x"), NULL);
  }

  public function testGetError() : void {
    global $errors;
    $this->assertEquals(get_error("email"), $errors["email"]);
    $this->assertEquals(get_error("x"), NULL);
  }

  public function testChosen() : void {
    global $post_params;
    $this->assertTrue(chosen("languages", "ga"));
    $this->assertFalse(chosen("languages", "fr"));
    $this->assertTrue(chosen("county", "wicklow"));
    $this->assertFalse(chosen("county", "dublin"));
  }

  public function testExceptionOccurred() : void {
    global $errors;
    $this->assertFalse(exception_occurred());
    $errors[KEY_EXCEPTION] = "Exception!";
    $this->assertTrue(exception_occurred());
  }

  public function testGetException() : void {
    global $errors;
    $this->assertEquals(get_exception(), "");
    $errors[KEY_EXCEPTION] = "Exception!";
    $this->assertEquals(get_exception(), $errors[KEY_EXCEPTION]);
  }

  protected function tearDown() : void {
  }
}
