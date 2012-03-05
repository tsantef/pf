<?php

function success_message($message) { echo colorize($message, 32); }
function info_message($message) { echo colorize($message, 36); }
function falure_message($message) { echo colorize($message, 31); }

function echo_item($name, $id, $description=null) {
    if ($description != null) {
        echo bwhite($name)." - $description (ID:".teal($id).")".PHP_EOL;
    } else {
        echo bwhite($name)." (ID:".teal($id).")".PHP_EOL;
    }
}

function colorize($str, $color) {
    return sprintf("\033[0;".$color."m%s\033[0m", $str);
}

function bwhite($str) {
    return colorize($str, "1;37");
}

function teal($str) {
    return colorize($str, "36"); 
}