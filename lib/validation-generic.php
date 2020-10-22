<?php
function get_post_params($allowed_params=[]) {
	$allowed_array = [];
	foreach($allowed_params as $param) {
		if(isset($_POST[$param])) {
			$allowed_array[$param] = $_POST[$param];
		}
    else {
			$allowed_array[$param] = NULL;
		}
	}
	return $allowed_array;
}
function is_present($value) {
	if (is_array($value)) {
    return TRUE;
  }
  else {
    $trimmed_value = trim($value);
    return isset($trimmed_value) && $trimmed_value !== "";
  }
}
function has_length($value, $options=[]) {
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
function has_no_html_tags($value) {
  return strcmp($value, strip_tags($value)) === 0;
}
function is_safe_email($email) {
  $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
  return strcmp($email, $sanitized_email) === 0;
}
function is_valid_email($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE;
}
function is_safe_float($float) {
  $sanitized_float = filter_var($float, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  return strcmp($float, $sanitized_float) === 0;
}
function is_valid_float($float) {
  $options = array(
    'options' => [ "decimal" => "."],
    'flags' => FILTER_FLAG_ALLOW_FRACTION,
  );
  return filter_var($float, FILTER_VALIDATE_FLOAT, $options) !== FALSE;
}
function is_safe_integer($integer) {
  $sanitized_integer = filter_var($integer, FILTER_SANITIZE_NUMBER_INT);
  return strcmp($integer, $sanitized_integer) === 0;
}
function is_valid_integer($integer, $range = []) {
  $options = array("options" => $range);
  return filter_var($integer, FILTER_VALIDATE_INT, $options) !== FALSE;
}
function is_valid_boolean($boolean) {
  return filter_var($boolean, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== NULL;
}
function is_match($value, $regex='//') {
  return preg_match($regex, $value) === 1;
}
function is_element($value, $set=[]) {
  return in_array($value, $set);
}
function is_subset($values, $set=[]) {
  if (!is_array($values)) {
    return FALSE;
  }
  else {
    return (count(array_diff($values, $set)) === 0);
  }
}
?>
