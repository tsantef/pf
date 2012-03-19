<?php
function pf_logout($argv) {
	$phpfog = new PHPFog(false);
	$phpfog->logout();
	return true;
}
?>
