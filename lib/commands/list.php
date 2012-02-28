<?php
function pf_list($argv) {
    switch(array_shift($argv)) {
        case "apps":
            echo "You chose apps\n";
            break;
        case "clouds":
            echo "You chose clouds\n";
            break;
        case "sshkeys":
            echo "You chose sshkeys\n";
            break;
        default:
            echo "Invalid command, try again\n";
            break;
    }
}
?>
