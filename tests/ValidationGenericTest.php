<?php declare(strict_types=1);

require_once "config.php";
require_once 'lib/validation-generic.php';

use PHPUnit\Framework\TestCase;

$errors = [];

final class ValidationGenericTest extends TestCase {

  protected function setUp() : void {
  }

  public function testIsPresent() : void {
    $this->assertEquals(is_present(""), FALSE);
    $this->assertEquals(is_present("  "), FALSE);
    $this->assertEquals(is_present([]), TRUE);
    $this->assertEquals(is_present(" x "), TRUE);
    $this->assertEquals(is_present("x"), TRUE);
  }

  public function testHasLength() : void {
    $this->assertEquals(has_length(""), TRUE);
    $this->assertEquals(has_length("x"), TRUE);
    $this->assertEquals(has_length("", ["min" => 5, "max" => 10]), FALSE);
    $this->assertEquals(has_length("xxx", ["min" => 5]), FALSE);
    $this->assertEquals(has_length("xxxxx", ["min" => 5]), TRUE);
    $this->assertEquals(has_length("xxxxx", ["max" => 5]), TRUE);
    $this->assertEquals(has_length("xxxxxxx", ["max" => 5]), FALSE);
    $this->assertEquals(has_length("xxxxx", ["exact" => 5]), TRUE);
    $this->assertEquals(has_length("xxx", ["exact" => 5]), FALSE);
    $this->assertEquals(has_length("x", ["min" => 3, "max" => 5]), FALSE);
    $this->assertEquals(has_length("xxx", ["min" => 3, "max" => 5]), TRUE);
    $this->assertEquals(has_length("xxxx", ["min" => 3, "max" => 5]), TRUE);
    $this->assertEquals(has_length("xxxxxx", ["min" => 3, "max" => 5]), FALSE);
  }

  public function testHasNoHtmlTags() : void {
    $this->assertEquals(has_no_html_tags(""), TRUE);
    $this->assertEquals(has_no_html_tags("xxx"), TRUE);
    $this->assertEquals(has_no_html_tags("<p>xxx</p>"), FALSE);
    $this->assertEquals(has_no_html_tags("<p>xxx"), FALSE);
    $this->assertEquals(has_no_html_tags("xxx</p>"), FALSE);
    $this->assertEquals(has_no_html_tags("<pxxx"), FALSE);
    $this->assertEquals(has_no_html_tags("p>xxx"), TRUE);
    $this->assertEquals(has_no_html_tags("xxx</p"), FALSE);
    $this->assertEquals(has_no_html_tags("xxx/p>"), TRUE);
  }

  public function testIsSafeEmail() : void {
    $this->assertEquals(is_safe_email("fred@bloggs.com"), TRUE);
    $this->assertEquals(is_safe_email("fred@"), TRUE);
    $this->assertEquals(is_safe_email("@bloggs.com"), TRUE);
    $this->assertEquals(is_safe_email("fredatbloggs.com"), TRUE);
    $this->assertEquals(is_safe_email("fred(@)bloggs.com"), FALSE);
    $this->assertEquals(is_safe_email("fred£@bloggs.com"), FALSE);
    $this->assertEquals(is_safe_email("fred:@bloggs.com"), FALSE);
  }

  public function testIsValidEmail() : void {
    $this->assertEquals(is_valid_email("fred@bloggs.com"), TRUE);
    $this->assertEquals(is_valid_email("a@b.co"), TRUE);
    $this->assertEquals(is_valid_email("fred@"), FALSE);
    $this->assertEquals(is_valid_email("@bloggs.com"), FALSE);
    $this->assertEquals(is_valid_email("fredatbloggs.com"), FALSE);
  }

  public function testIsSafeFloat() : void {
    $this->assertEquals(is_safe_float("1.1"), TRUE);
    $this->assertEquals(is_safe_float("1."), TRUE);
    $this->assertEquals(is_safe_float(".1"), TRUE);
    $this->assertEquals(is_safe_float("1"), TRUE);
    $this->assertEquals(is_safe_float("."), TRUE);
    $this->assertEquals(is_safe_float("1.10"), TRUE);
    $this->assertEquals(is_safe_float("1.1O"), FALSE);
    $this->assertEquals(is_safe_float("6B3A"), FALSE);
  }

  public function testIsValidFloat() : void {
    $this->assertEquals(is_valid_float("1.1"), TRUE);
    $this->assertEquals(is_valid_float("1."), TRUE);
    $this->assertEquals(is_valid_float(".1"), TRUE);
    $this->assertEquals(is_valid_float("1"), TRUE);
    $this->assertEquals(is_valid_float("."), FALSE);
    $this->assertEquals(is_valid_float("1.10"), TRUE);
  }

  public function testIsSafeInteger() : void {
    $this->assertEquals(is_safe_integer("10"), TRUE);
    $this->assertEquals(is_safe_integer("+10"), TRUE);
    $this->assertEquals(is_safe_integer("-10"), TRUE);
    $this->assertEquals(is_safe_integer("1.0"), FALSE);
    $this->assertEquals(is_safe_integer("1."), FALSE);
    $this->assertEquals(is_safe_integer(".0"), FALSE);
    $this->assertEquals(is_safe_integer("6B3A"), FALSE);
  }

  public function testIsValidInteger() : void {
    $this->assertEquals(is_valid_integer("10"), TRUE);
    $this->assertEquals(is_valid_integer("+10"), TRUE);
    $this->assertEquals(is_valid_integer("-10"), TRUE);
    $this->assertEquals(is_valid_integer("1.0"), FALSE);
    $this->assertEquals(is_valid_integer("1."), FALSE);
    $this->assertEquals(is_valid_integer(".0"), FALSE);
    $this->assertEquals(is_valid_integer("6B3A"), FALSE);
  }

  public function testIsValidBoolean() : void {
    $this->assertEquals(is_valid_boolean("1"), TRUE);
    $this->assertEquals(is_valid_boolean("0"), TRUE);
    $this->assertEquals(is_valid_boolean("on"), TRUE);
    $this->assertEquals(is_valid_boolean("off"), TRUE);
    $this->assertEquals(is_valid_boolean("yes"), TRUE);
    $this->assertEquals(is_valid_boolean("no"), TRUE);
    $this->assertEquals(is_valid_boolean("true"), TRUE);
    $this->assertEquals(is_valid_boolean("false"), TRUE);
    $this->assertEquals(is_valid_boolean(""), TRUE);
    $this->assertEquals(is_valid_boolean("null"), FALSE);
  }

  public function testIsMatch() : void {
    $this->assertEquals(is_match("111", '/[0-9]{3}/'), TRUE);
    $this->assertEquals(is_match("111", '/[A-Z]{3}/'), FALSE);
  }

  public function testIsElement() : void {
    $this->assertEquals(is_element(1, [1, 2, 3]), TRUE);
    $this->assertEquals(is_element(1, [2, 3, 4]), FALSE);
    $this->assertEquals(is_element("a", ["a", "b", "c"]), TRUE);
    $this->assertEquals(is_element("a", ["b", "c", "d"]), FALSE);
  }

  public function testIsSubset() : void {
    $this->assertEquals(is_subset([1, 2], [1, 2, 3]), TRUE);
    $this->assertEquals(is_subset([1, 2], [2, 3, 4]), FALSE);
    $this->assertEquals(is_subset(["a", "b"], ["a", "b", "c"]), TRUE);
    $this->assertEquals(is_subset(["a", "b"], ["b", "c", "d"]), FALSE);
  }

  protected function tearDown() : void {
  }
}
