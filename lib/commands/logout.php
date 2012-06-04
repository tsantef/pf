<?php
function pf_logout($argv) {
    $phpfog   = new PHPFog(false);
    $username = array_shift($argv);

    if ($username == null) {
        if ($phpfog->logout()) {
            success_message("Logged out");
        } else {
            failure_message("Not logged in");
        }

        return true;
    }

    if ($phpfog->logout($username)) {
        success_message("Logged out {$username}");
    } else {
        failure_message("Not logged in");
    }

    return true;
}
