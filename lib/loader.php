<?php
define("HOME", str_replace("/", DS, $_SERVER['HOME'].'/'));
foreach (glob(LIB_PATH."deps/*.php") as $dep) {
    require_once $dep;
}
