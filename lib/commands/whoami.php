<?php
function pf_whoami($argv) {
    // Prevent php errors when not logged in
    error_reporting(E_ALL ^ E_NOTICE);

    $phpfog = new PHPFog(false);

    if ($phpfog->username() == null) {
        failure_message("Not logged in");
    } else {
        info_message("Logged in as {$phpfog->username()}");
    }

    return true;
}
