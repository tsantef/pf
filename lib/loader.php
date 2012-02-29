<?php

define("HOME", $_SERVER['HOME'].'/');

foreach (glob(LIB_PATH."deps/*.php") as $dep) {
    require_once $dep;
}
