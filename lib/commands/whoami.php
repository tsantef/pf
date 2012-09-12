<?php
function pf_whoami($argv) {
    $phpfog = new PHPFog(false);

    if ($phpfog->username() == null) {
        failure("Not logged in");
    } else {
        info("Logged in as {$phpfog->username()}");
    }

    return true;
}
