<?php
function get_value($key) {
  global $post_params;
  if (isset($post_params) && is_array($post_params) && array_key_exists($key, $post_params)) {
    return $post_params[$key];
  }
}
function get_error($key) {
  global $errors;
  if (isset($errors) && is_array($errors) && array_key_exists($key, $errors)) {
    return $errors[$key];
  }
  else {
    return "";
  }
}
function chosen($key, $search) {
  global $post_params;
  $chosen = FALSE;
  if (isset($post_params) && is_array($post_params) && array_key_exists($key, $post_params)) {
    $value = $post_params[$key];
    if (is_array($value)) {
      $chosen = in_array($search, $value);
    }
    else if (is_string($value)) {
      $chosen = (strcmp($search, $value) === 0);
    }
  }
  return $chosen;
}
?>
