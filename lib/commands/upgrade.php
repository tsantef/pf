<?php
function pf_upgrade() {
    system("php ".dirname(__FILE__)."/../../bin/installer -- --update", $result);
    if ($result !== 0) {
        failure_message("Upgrade Failed.");

        exit(1);
    } else {
        success_message("Upgrade Successful.");
    }

    return true;
}
