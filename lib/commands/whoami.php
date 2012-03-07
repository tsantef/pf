<?php
function pf_whoami($argv) {
    $phpfog = new PHPFog();
    if ($phpfog->session['username'] == null) {
        failure_message("Not logged in");
    } else {
        echo wrap("Logged in as ".colorize($phpfog->session['username'], "1;37"));
    }
    return true;
}
?>
