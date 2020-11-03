<?php
class HttpRequest {
  public $method = null;
  public $uri = null;
  public $headers = null;
  public $cookies = null;
  private $data = null;
  private $errors = null;

  public function __construct($data = null) {
    $this->init_request_method();
    $this->init_request_uri();
    $this->init_request_headers();
    $this->init_request_cookies();
    $this->init_request_data($data);
  }
  private function init_request_uri() {
    if (isset($_SERVER) && is_array($_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)) {
      $this->uri = $_SERVER['REQUEST_METHOD'];
    }
  }
  private function init_request_method() {
    if (isset($_SERVER) && is_array($_SERVER) && array_key_exists('REQUEST_METHOD', $_SERVER)) {
      switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
        case 'POST':
          $this->method = $_SERVER['REQUEST_METHOD'];
          break;
        default:
          throw new InvalidArgumentException('Unexpected request method.');
          break;
      }
    }
  }
  private function init_request_headers() {
    if (function_exists('getallheaders')) {
      $this->headers = getallheaders();
    }
  }
  private function init_request_cookies() {
    if (isset($_COOKIE) && is_array($_COOKIE)) {
      $this->cookies = $_COOKIE;
    }
  }
  private function init_request_data($data) {
    if ($data !== null) {
      $this->data = $data;
    }
    else {
      switch ($this->method) {
        case 'GET':
          $this->data = $_GET;
          break;
        case 'POST':
          $this->data = $_POST;
          break;
      }
    }
  }

  private function is_present($key) {
    $value = $this->data[$key];
  	if (is_array($value)) {
      return TRUE;
    }
    else {
      $trimmed_value = trim($value);
      return isset($trimmed_value) && $trimmed_value !== "";
    }
  }
  private function has_length($key, $options=[]) {
    $value = $this->data[$key];
  	if(isset($options['max']) && (strlen($value) > (int)$options['max'])) {
  		return false;
  	}
  	if(isset($options['min']) && (strlen($value) < (int)$options['min'])) {
  		return false;
  	}
  	if(isset($options['exact']) && (strlen($value) != (int)$options['exact'])) {
  		return false;
  	}
  	return true;
  }
  private function has_no_html_tags($key) {
    $value = $this->data[$key];
    return strcmp($value, strip_tags($value)) === 0;
  }
  private function is_safe_email($key) {
    $email = $this->data[$key];
    $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return strcmp($email, $sanitized_email) === 0;
  }
  private function is_valid_email($key) {
    $email = $this->data[$key];
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
  }
  private function is_safe_float($key) {
    $float = $this->data[$key];
    $sanitized_float = filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    return strcmp($float, $sanitized_float) === 0;
  }
  private function is_valid_float($key) {
    $float = $this->data[$key];
    $options = array(
      'options' => [ "decimal" => "."],
      'flags' => FILTER_FLAG_ALLOW_FRACTION,
    );
    return filter_var($float, FILTER_VALIDATE_FLOAT, $options) !== FALSE;
  }
  private function is_safe_integer($key) {
    $integer = $this->data[$key];
    $sanitized_integer = filter_var($integer, FILTER_SANITIZE_NUMBER_INT);
    return strcmp($integer, $sanitized_integer) === 0;
  }
  private function is_valid_integer($key, $range = []) {
    $integer = $this->data[$key];
    $options = array("options" => $range);
    return filter_var($integer, FILTER_VALIDATE_INT, $options) !== FALSE;
  }
  private function is_valid_boolean($key) {
    $boolean = $this->data[$key];
    return filter_var($boolean, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== NULL;
  }
  private function is_match($key, $regex='//') {
    $value = $this->data[$key];
    return preg_match($regex, $value) === 1;
  }
  private function is_element($key, $set=[]) {
    $value = $this->data[$key];
    return in_array($value, $set);
  }
  private function is_subset($key, $set=[]) {
    $values = $this->data[$key];
    if (!is_array($values)) {
      return FALSE;
    }
    else {
      return (count(array_diff($values, $set)) === 0);
    }
  }

  private function validate_rule($key, $rule_str) {
    $valid = TRUE;
    $rule_parts = explode(":", $rule_str);
    $rule_name = $rule_parts[0];
    switch ($rule_name) {
      case "present" :
        if (!$this->is_present($key)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a value for " . $key;
        }
        break;
      case "minlength" :
        $min = $rule_parts[1];
        if (!$this->has_length($key, ["min" => $min])) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter at least " .$min . " characters for " . $key;
        }
        break;
      case "maxlength" :
        $max = $rule_parts[1];
        if (!$this->has_length($key, ["max" => $max])) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter at most " .$max . " characters for " . $key;
        }
        break;
      case "email" :
        if (!$this->is_safe_email($key) || !$this->is_valid_email($key)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid email for " . $key;
        }
        break;
      case "float" :
        if (!$this->is_safe_float($key) || !$this->is_valid_float($key)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid float for " . $key;
        }
        break;
      case "integer" :
        if (!$this->is_safe_integer($key) || !$this->is_valid_integer($key)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid integer for " . $key;
        }
        break;
      case "min" :
        $min = $rule_parts[1];
        if (!$this->is_safe_integer($key) || !$this->is_valid_integer($key, ["min_range" => $min])) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid integer greater than or equal to " . $min ." for " . $key;
        }
        break;
      case "max" :
        $max = $rule_parts[1];
        if (!$this->is_safe_integer($key) || !$this->is_valid_integer($key, ["max_range" => $max])) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid integer less than or equal to " . $max ." for " . $key;
        }
        break;
      case "boolean" :
        if (!$this->is_safe_boolean($key) || !$this->is_valid_boolean($key)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a valid boolean for " . $key;
        }
        break;
      case "match" :
        $regex = substr($rule_str, strpos($rule_str, ":") + 1);
        if (!$this->is_match($key, $regex)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter string that matches the pattern " . $regex . " for " . $key;
        }
        break;
      case "in" :
        $values_string = $rule_parts[1];
        $values_array = explode(",", $values_string);
        if (!$this->is_element($key, $values_array)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a value in the list " . $values_string . " for " . $key;
        }
        break;
      case "not_in" :
        $values_string = $rule_parts[1];
        $values_array = explode(",", $values_string);
        if ($this->is_element($key, $values_array)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter a value not in the list " . $values_string . " for " . $key;
        }
        break;
      case "subset" :
        $values_string = $rule_parts[1];
        $values_array = explode(",", $values_string);
        if (!$this->is_subset($key, $values_array)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter values in the list " . $values_string . " for " . $key;
        }
        break;
      case "not_subset" :
        $values_string = $rule_parts[1];
        $values_array = explode(",", $values_string);
        if ($this->is_subset($key, $values_array)) {
          $valid = FALSE;
          $this->errors[$key] = "Please enter values not in the list " . $values_string . " for " . $key;
        }
        break;
    }
    return $valid;
  }
  public function validate($rules=[]) {
    $this->errors = [];
    foreach ($rules as $field_name => $field_rules_str) {
      $field_rules_array = explode("|", $field_rules_str);
      foreach ($field_rules_array as $field_rule_str) {
        if (!$this->validate_rule($field_name, $field_rule_str)) {
          break;
        }
      }
    }
  }

  public function input($key) {
    if (isset($this->data) && is_array($this->data) && array_key_exists($key, $this->data)) {
      return $this->data[$key];
    }
    else {
      return null;
    }
  }
  public function is_valid() {
    return isset($this->errors) && is_array($this->errors) && empty($this->errors);
  }
  public function error($key) {
    if (isset($this->errors) && is_array($this->errors) && array_key_exists($key, $this->errors)) {
      return $this->errors[$key];
    }
    else {
      return null;
    }
  }
  public function set_error($key, $value) {
    if (isset($this->errors) && is_array($this->errors) && !array_key_exists($key, $this->errors)) {
      $this->errors[$key] = $value;
    }
  }
  public function chosen($key, $search) {
    $chosen = FALSE;
    if (isset($this->data) && is_array($this->data) && array_key_exists($key, $this->data)) {
      $value = $this->data[$key];
      if (is_array($value)) {
        $chosen = in_array($search, $value);
      }
      else if (is_string($value)) {
        $chosen = (strcmp($search, $value) === 0);
      }
    }
    return $chosen;
  }
  public function has_exception() {
    return (isset($this->errors) && is_array($this->errors) && array_key_exists(KEY_EXCEPTION, $this->errors));
  }
  public function get_exception() {
    if (isset($this->errors) && is_array($this->errors) && array_key_exists(KEY_EXCEPTION, $this->errors)) {
      return $this->errors[KEY_EXCEPTION];
    }
    else {
      return null;
    }
  }
  public function set_exception($message) {
    if (isset($this->errors) && is_array($this->errors) && !array_key_exists(KEY_EXCEPTION, $this->errors)) {
      $this->errors[KEY_EXCEPTION] = $message;
    }
  }
}
?>
