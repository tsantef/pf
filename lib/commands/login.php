<?php
function pf_login($argv) {
  // Prevent php errors when not logged in
  error_reporting(E_ALL ^ E_NOTICE);

  $phpfog = new PHPFog(false);

  if ($phpfog->username() == null) {
    try {
        $has_api = $phpfog->login();
    } catch (Exception $e) {
        failure_message("Something blew up during login!");
    }
    if ($has_api) {
        success_message("Logged in as {$phpfog->username()}");
    } else {
        die(wrap(red('Failed to login')));
    }
  } else {
      info_message("Already logged in as {$phpfog->username()}, please logout first.");
  }
}
?>
