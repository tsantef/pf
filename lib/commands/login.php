<?php
function pf_login($argv) {
  // Prevent php errors when not logged in
  error_reporting(E_ALL ^ E_NOTICE);

  $phpfog = new PHPFog(false);
  $username = array_shift($argv);

  if ($phpfog->username() == null) {
    try {
        $has_api = $phpfog->login($username);
    } catch (PestJSON_Unauthorized $e) {
        failure_message("Invalid login or password. Please try again.");
        exit(1);
    } catch (Exception $e) {
        failure_message("Error: ".$e->getMessage());
        exit(1);
    }
    if ($has_api) {
        success_message("Logged in as {$phpfog->username()}");
        return true;
    } else {
        die(wrap(red('Failed to login')));
    }
  } else {
      info_message("Already logged in as {$phpfog->username()}, please logout first.");
      return true;
  }
}
?>
