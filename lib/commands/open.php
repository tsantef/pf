<?php
function pf_open($argv) {
    if (array_shift($argv) == "app") {
        $app_id = array_shift($argv);
    } else {
        echo "Invalid command, try again\n";
    }
}
?>
