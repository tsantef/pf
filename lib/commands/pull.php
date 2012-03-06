<?php

function pf_pull($argv) {
    $phpfog = new PHPFog();

    system("git pull");

    return true;
}