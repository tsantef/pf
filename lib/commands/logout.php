<?php

function pf_logout($argv) {
	$phpfog = new PHPFog();
	$phpfog->logout();

	return true;
}