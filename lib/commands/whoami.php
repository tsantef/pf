<?php
function pf_whoami($argv) {
    $phpfog = new PHPFog(false);

    if ($phpfog->username() == null) {
        failure_message("Not logged in");
    } else {
        info_message("Logged in as {$phpfog->username()}");
    }

    return true;
}
