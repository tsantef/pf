<?php
function pf_push($argv) {
    $phpfog = new PHPFog();
    system("git push");
    return true;
}
?>
