<?php

define("HOME", $_SERVER['HOME'].'/');
define("LIB_PATH", basename(dirname(__FILE__)).'/');

foreach (glob(LIB_PATH."deps/*.php") as $dep) {
    require_once $dep;
}
