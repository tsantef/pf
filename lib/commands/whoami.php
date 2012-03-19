<?php
function pf_whoami($argv) {
    $phpfog = new PHPFog(false);
    if ($phpfog->session['username'] == null) {
        failure_message("Not logged in");
    } else {
        echo wrap("Logged in as ".bwhite($phpfog->session['username']));
    }
    return true;
}
?>
