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

  public function testExceptions() : void {
    $request = new HttpRequest([
      "string_01" => "abc",
      "string_02" => "abc",
      "string_03" => "abc",
      "string_04" => "abc",
      "string_05" => "abc",
      "string_06" => "abc",
      "string_07" => "abc",
      "string_08" => "abc",
      "string_09" => "abc",
      "string_10" => "abc",
      "string_11" => "abc",
      "string_12" => "abc",
      "string_13" => "abc",
      "string_14" => "abc",
      "string_15" => "abc",
      "string_16" => "abc",
      "string_17" => "abc",
      "string_18" => "abc",
      "string_19" => "abc",
      "string_20" => "abc",
      "string_21" => "abc",
      "string_22" => "abc",
      "string_23" => "abc",
      "string_24" => "abc",
      "string_25" => "abc",
      "string_26" => "abc",
      "string_27" => "abc",
      "string_28" => "abc",
      "string_29" => "abc",
      "string_30" => "abc",
      "string_31" => "abc",
      "string_32" => "abc",
      "string_33" => "abc",
      "string_34" => "abc",
      "string_35" => "abc",
      "string_36" => "abc",
      "string_37" => "abc",
      "string_38" => "abc",
      "string_39" => "abc",
      "string_40" => "abc",
      "string_41" => "abc"
    ]);
    $rules = [
      "string_01" => "",
      "string_02" => "present:",
      "string_03" => "present:abc",
      "string_04" => "minlength",
      "string_05" => "minlength:",
      "string_06" => "minlength:abc",
      "string_07" => "minlength:1.0",
      "string_08" => "minlength:-1",
      "string_09" => "maxlength",
      "string_10" => "maxlength:abc",
      "string_11" => "maxlength:1.0",
      "string_12" => "maxlength:-1",
      "string_13" => "email:",
      "string_14" => "email:abc",
      "string_16" => "float:",
      "string_17" => "float:1..0",
      "string_18" => "float:abc",
      "string_20" => "integer:",
      "string_21" => "integer:1.0",
      "string_22" => "integer:abc",
      "string_23" => "min",
      "string_24" => "min:abc",
      "string_25" => "min:1.0",
      "string_26" => "min:",
      "string_27" => "max",
      "string_28" => "max:abc",
      "string_29" => "max:1.0",
      "string_30" => "max:",
      "string_31" => "boolean:",
      "string_32" => "boolean:abc",
      "string_33" => "match:",
      "string_34" => "match:abc",
      "string_35" => "match",
      "string_36" => "in",
      "string_37" => "in:",
      "string_38" => "not_in",
      "string_39" => "not_in:",
      "string_40" => "subset",
      "string_41" => "subset:",
      "string_40" => "not_subset",
      "string_41" => "not_subset:"
    ];
    foreach ($rules as $key => $rule) {
      $ex = null;
      try {
        $request->validate([$key => $rule]);
      }
      catch(Exception $e) {
        $ex = $e;
      }
      $this->assertNotNull($ex);
    }
  }

  protected function tearDown() : void {
  }
}
