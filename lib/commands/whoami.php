<?php
function pf_whoami($argv) {
    $phpfog = new PHPFog();
    if ($phpfog->session['username'] == null) {
        falure_message("Not logged in".PHP_EOL);
    } else {
        echo "Logged in as " . colorize($phpfog->session['username'], "1;37") . PHP_EOL;
    }
    return true;
}
?>
