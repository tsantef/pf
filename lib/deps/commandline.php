<?php

class CommandLine {
    static public function run($argv) {
        if (count($argv) < 1) {
            return false;
        }

        $arg = array_shift($argv);
        $cmd = "pf_".$arg;
        $cmd_file = LIB_PATH."commands/".$arg.".php";

        if (file_exists($cmd_file)) {
            require_once $cmd_file;
            return $cmd($argv);
        } else {
            return false;
        }

        return true;
    }
}
