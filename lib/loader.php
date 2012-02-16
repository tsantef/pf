<?php

define("LIB_PATH", dirname(__FILE__));

function load_lib($path) {
	
	require join('/', array(LIB_PATH, trim($path, '/')));

}

require 'pf/commandline.php';