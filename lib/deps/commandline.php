<?php

class CommandLine {
    static public function run($argv, $argc) {
        if ($argc < 1) {
            return false;
        }

        $cmd = "pf_".$argv[0];
        $cmd_file = LIB_PATH."commands/".$argv[0].".php";

        if (file_exists($cmd_file)) {
            require_once $cmd_file;
            $cmd($argv);
        }

        return true;
    }
}
