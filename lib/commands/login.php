<?php
function pf_login($argv) {
  # Test Connection to PHPFog API
  $phpfog = new PHPFog(false);
  try {
      $has_api = $phpfog->login();
  } catch (Exception $e) {
      echo wrap("Something blew up during login!");
  }
  if (!$has_api) {
      die(wrap('Failed to login'));
  }
}
?>
