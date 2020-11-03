<?php declare(strict_types=1);

require_once "config.php";

use PHPUnit\Framework\TestCase;

final class HttpRequestTest extends TestCase {

  protected function setUp() : void {
  }

  public function testIsPresent() : void {
    $request = new HttpRequest([
      "email" => "",
      "password" => "secret",
      "name" => "  "
    ]);
    $rules = [
      "email" => "present",
      "password" => "present",
      "name" => "present"
    ];
    $request->validate($rules);
    $this->assertNotNull($request->error("email"));
    $this->assertNull($request->error("password"));
    $this->assertNotNull($request->error("name"));
  }

  public function testHasLength() : void {
    $request = new HttpRequest([
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
    $request->validate($rules);
    $this->assertNotNull($request->error("email"));
    $this->assertNull($request->error("password"));
    $this->assertNotNull($request->error("address"));
    $this->assertNull($request->error("city"));
    $this->assertNotNull($request->error("county"));
    $this->assertNull($request->error("country"));
  }

  public function testEmail() : void {
    $request = new HttpRequest([
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
    $request->validate($rules);
    $this->assertNull($request->error("email_01"));
    $this->assertNotNull($request->error("email_02"));
    $this->assertNotNull($request->error("email_03"));
    $this->assertNotNull($request->error("email_04"));
    $this->assertNotNull($request->error("email_05"));
    $this->assertNotNull($request->error("email_06"));
  }

  public function testFloat() : void {
    $request = new HttpRequest([
      "float_01" => "10",
      "float_02" => "10.1",
      "float_03" => ".1"
    ]);
    $rules = [
      "float_01" => "float",
      "float_02" => "float",
      "float_03" => "float"
    ];
    $request->validate($rules);
    $this->assertNull($request->error("float_01"));
    $this->assertNull($request->error("float_02"));
    $this->assertNull($request->error("float_03"));
  }

  public function testInteger() : void {
    $request = new HttpRequest([
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
    $request->validate($rules);
    $this->assertNull($request->error("integer_01"));
    $this->assertNotNull($request->error("integer_02"));
    $this->assertNotNull($request->error("integer_03"));
    $this->assertNull($request->error("integer_04"));
    $this->assertNotNull($request->error("integer_05"));
    $this->assertNull($request->error("integer_06"));
    $this->assertNotNull($request->error("integer_07"));
  }

  public function testMatch() : void {
    $request = new HttpRequest([
      "string_01" => "123",
      "string_02" => "abc"
    ]);
    $rules = [
      "string_01" => "match:/[0-9]{3}/",
      "string_02" => "match:/[0-9]{3}/"
    ];
    $request->validate($rules);
    $this->assertNull($request->error("string_01"));
    $this->assertNotNull($request->error("string_02"));
  }

  public function testIn() : void {
    $request = new HttpRequest([
      "string_01" => "1",
      "string_02" => "4"
    ]);
    $rules = [
      "string_01" => "in:1,2,3",
      "string_02" => "in:1,2,3"
    ];
    $request->validate($rules);
    $this->assertNull($request->error("string_01"));
    $this->assertNotNull($request->error("string_02"));
  }

  public function testNotIn() : void {
    $request = new HttpRequest([
      "string_01" => "4",
      "string_02" => "1"
    ]);
    $rules = [
      "string_01" => "not_in:1,2,3",
      "string_02" => "not_in:1,2,3"
    ];
    $request->validate($rules);
    $this->assertNull($request->error("string_01"));
    $this->assertNotNull($request->error("string_02"));
  }

  public function testSubset() : void {
    $request = new HttpRequest([
      "string_01" => [1,2],
      "string_02" => [4,5],
      "string_03" => [5,6]
    ]);
    $rules = [
      "string_01" => "subset:1,2,3,4",
      "string_02" => "subset:1,2,3,4",
      "string_03" => "subset:1,2,3,4"
    ];
    $request->validate($rules);
    $this->assertNull($request->error("string_01"));
    $this->assertNotNull($request->error("string_02"));
    $this->assertNotNull($request->error("string_03"));
  }

  public function testNotSubset() : void {
    $request = new HttpRequest([
      "string_01" => [1,2],
      "string_02" => [4,5],
      "string_03" => [5,6],
    ]);
    $rules = [
      "string_01" => "not_subset:1,2,3,4",
      "string_02" => "not_subset:1,2,3,4",
      "string_03" => "not_subset:1,2,3,4"
    ];
    $request->validate($rules);
    $this->assertNotNull($request->error("string_01"));
    $this->assertNull($request->error("string_02"));
    $this->assertNull($request->error("string_03"));
  }

  protected function tearDown() : void {
  }
}
