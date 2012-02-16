<?php

define("HOME", $_SERVER['HOME']);
define("LIB_PATH", dirname(__FILE__));

require 'commandline.php';
require 'shell.php';

function command_loader($class_name) {

    $class_name = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $class_name);
    $path = join(DIRECTORY_SEPARATOR, array(LIB_PATH, $class_name.".php"));

    if (file_exists($path)) {
        require $path;
    }

} spl_autoload_register('command_loader');