<?php
function pf_logout($argv) {
    $phpfog   = new PHPFog();
    $username = array_shift($argv);

    if ($username == null) {
        if ($phpfog->logout()) {
            success("Logged out");
        } else {
            failure("Not logged in");
        }

        return true;
    }

    if ($phpfog->logout($username)) {
        success("Logged out {$username}");
    } else {
        failure("Not logged in");
    }

    return true;
}
