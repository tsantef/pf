<?php
define("USECOLOR", usecolor());

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

function usecolor() {
    if (!function_exists("posix_isatty")) { return false; }
    if (!posix_isatty(STDOUT)) { return false; }
    return true;
}

function colorize($str, $color) {
    if (USECOLOR) {
        return sprintf("\033[0;".$color."m%s\033[0m", $str);
    } else {
        return $str;
    }
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
