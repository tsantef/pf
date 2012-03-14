<?php
function wrap($msg) {
    return $msg.PHP_EOL;
}

function success_message($message) {
    echo wrap(colorize($message, 32));
}
function info_message($message) {
    echo wrap(colorize($message, 36));
}
function failure_message($message) {
    echo wrap(colorize($message, 31));
}

function echo_item($name, $id, $description = null) {
    if ($description != null) {
        echo wrap(bwhite($name)." - $description (ID: ".teal($id).")");
    } else {
        echo wrap(bwhite($name)." (ID: ".teal($id).")");
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

function red($str) {
    return colorize($str, "31");
}

function green($str) {
    return colorize($str, "32");
}
?>
