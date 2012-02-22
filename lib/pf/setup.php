<?php
namespace pf;

class setup {

    static public function run($argv) {

    	$phpfog = new PHPFogClient();

    	print_r($phpfog->get_sshkeys());

    	return true;
    }

}