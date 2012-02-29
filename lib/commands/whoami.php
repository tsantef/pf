<?php
function pf_whoami($argv) {
    $phpfog = new \pf\PHPFog();
    if ($phpfog->session['username'] == null) {
        echo "Not logged in".PHP_EOL;
    } else {
        echo "Logged in as \033[1;37m".$phpfog->session['username']."\033[0m".PHP_EOL;
    }
}
?>
