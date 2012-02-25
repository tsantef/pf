<?php

define("HOME", $_SERVER['HOME'].'/');
define("LIB_PATH", basename(dirname(__FILE__)).'/');

foreach (glob(LIB_PATH."deps/*.php") as $dep) {
    require_once $dep;
}

function command_loader($class) {
    $path = LIB_PATH."commands/".$class;
    if (file_exists($path)) {
        echo $path.PHP_EOL;
        require_once $path;
    }
}

spl_autoload_register('command_loader');
