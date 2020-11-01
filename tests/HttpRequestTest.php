<?php declare(strict_types=1);

require_once "config.php";

use PHPUnit\Framework\TestCase;

final class HttpRequestTest extends TestCase {

  private $request;

  protected function setUp() : void {
    $this->request = new HttpRequest();
  }

  public function testIsPresent() : void {
    $this->request->initialise([
      "email" => "",
      "password" => "secret",
      "name" => "  "
    ]);
    $rules = [
      "email" => "present",
      "password" => "present",
      "name" => "present"
    ];
    $this->request->validate($rules);
    $this->assertNotNull($this->request->error("email"));
    $this->assertNull($this->request->error("password"));
    $this->assertNotNull($this->request->error("name"));
  }

  public function testHasLength() : void {
    $this->request->initialise([
      "email" => "abc",
      "password" => "secret",
      "address" => "main street",
      "city" => "tuam",
      "county" => "galway",
      "country" => "ireland"
    ]);
    $rules = [
      "email" => "minlength:5",
      "password" => "minlength:5",
      "address" => "maxlength:5",
      "city" => "maxlength:5",
      "county" => "maxlength:5",
      "country" => "maxlength:7"
    ];
    $this->request->validate($rules);
    $this->assertNotNull($this->request->error("email"));
    $this->assertNull($this->request->error("password"));
    $this->assertNotNull($this->request->error("address"));
    $this->assertNull($this->request->error("city"));
    $this->assertNotNull($this->request->error("county"));
    $this->assertNull($this->request->error("country"));
  }

  public function testEmail() : void {
    $this->request->initialise([
      "email_01" => "fred@bloggs.com",
      "email_02" => "fred@.com",
      "email_03" => "@bloggs.com",
      "email_04" => "fred()@bloggs.com",
      "email_05" => "fred£@bloggs.com",
      "email_06" => "fredatbloggs.com",
    ]);
    $rules = [
      "email_01" => "email",
      "email_02" => "email",
      "email_03" => "email",
      "email_04" => "email",
      "email_05" => "email",
      "email_06" => "email",
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("email_01"));
    $this->assertNotNull($this->request->error("email_02"));
    $this->assertNotNull($this->request->error("email_03"));
    $this->assertNotNull($this->request->error("email_04"));
    $this->assertNotNull($this->request->error("email_05"));
    $this->assertNotNull($this->request->error("email_06"));
  }

  public function testFloat() : void {
    $this->request->initialise([
      "float_01" => "10",
      "float_02" => "10.1",
      "float_03" => ".1"
    ]);
    $rules = [
      "float_01" => "float",
      "float_02" => "float",
      "float_03" => "float"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("float_01"));
    $this->assertNull($this->request->error("float_02"));
    $this->assertNull($this->request->error("float_03"));
  }

  public function testInteger() : void {
    $this->request->initialise([
      "integer_01" => "10",
      "integer_02" => "10.1",
      "integer_03" => ".1",
      "integer_04" => "10",
      "integer_05" => "10",
      "integer_06" => "10",
      "integer_07" => "10"
    ]);
    $rules = [
      "integer_01" => "integer",
      "integer_02" => "integer",
      "integer_03" => "integer",
      "integer_04" => "integer|min:5",
      "integer_05" => "integer|min:15",
      "integer_06" => "integer|max:15",
      "integer_07" => "integer|max:5"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("integer_01"));
    $this->assertNotNull($this->request->error("integer_02"));
    $this->assertNotNull($this->request->error("integer_03"));
    $this->assertNull($this->request->error("integer_04"));
    $this->assertNotNull($this->request->error("integer_05"));
    $this->assertNull($this->request->error("integer_06"));
    $this->assertNotNull($this->request->error("integer_07"));
  }

  public function testMatch() : void {
    $this->request->initialise([
      "string_01" => "123",
      "string_02" => "abc"
    ]);
    $rules = [
      "string_01" => "match:/[0-9]{3}/",
      "string_02" => "match:/[0-9]{3}/"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("string_01"));
    $this->assertNotNull($this->request->error("string_02"));
  }

  public function testIn() : void {
    $this->request->initialise([
      "string_01" => "1",
      "string_02" => "4"
    ]);
    $rules = [
      "string_01" => "in:1,2,3",
      "string_02" => "in:1,2,3"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("string_01"));
    $this->assertNotNull($this->request->error("string_02"));
  }

  public function testNotIn() : void {
    $this->request->initialise([
      "string_01" => "4",
      "string_02" => "1"
    ]);
    $rules = [
      "string_01" => "not_in:1,2,3",
      "string_02" => "not_in:1,2,3"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("string_01"));
    $this->assertNotNull($this->request->error("string_02"));
  }

  public function testSubset() : void {
    $this->request->initialise([
      "string_01" => [1,2],
      "string_02" => [4,5],
      "string_03" => [5,6]
    ]);
    $rules = [
      "string_01" => "subset:1,2,3,4",
      "string_02" => "subset:1,2,3,4",
      "string_03" => "subset:1,2,3,4"
    ];
    $this->request->validate($rules);
    $this->assertNull($this->request->error("string_01"));
    $this->assertNotNull($this->request->error("string_02"));
    $this->assertNotNull($this->request->error("string_03"));
  }

  public function testNotSubset() : void {
    $this->request->initialise([
      "string_01" => [1,2],
      "string_02" => [4,5],
      "string_03" => [5,6],
    ]);
    $rules = [
      "string_01" => "not_subset:1,2,3,4",
      "string_02" => "not_subset:1,2,3,4",
      "string_03" => "not_subset:1,2,3,4"
    ];
    $this->request->validate($rules);
    $this->assertNotNull($this->request->error("string_01"));
    $this->assertNull($this->request->error("string_02"));
    $this->assertNull($this->request->error("string_03"));
  }

  protected function tearDown() : void {
  }
}
