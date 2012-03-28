<?php
function pf_logout($argv) {
  // Prevent php errors when not logged in
  error_reporting(E_ALL ^ E_NOTICE);

  $phpfog   = new PHPFog(false);
  $username = $phpfog->username();

  if ($username == null) {
      failure_message("Not logged in");
  } else {
      $phpfog->logout();
      success_message("Logged out {$username}");
  }

  return true;
}
?>
