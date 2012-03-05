<?php

function pf_list($argv) {
    $phpfog = new PHPFog();
    
    $arg = array_shift($argv);

    switch (strtolower($arg)) {
        case "apps":

        break;

        case "sshkeys":
            print_r($phpfog->get_sshkeys());
        break;
    }

}