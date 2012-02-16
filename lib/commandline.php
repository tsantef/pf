<?php 

class CommandLine {

    static public function run($argv) {
        if ($argv == null || !is_array($argv) || count($argv) < 1) {
            echo "Invalid Command";
            return false;
        }

        $command = "pf\\".str_replace("-","_",array_shift($argv));

        if (class_exists($command)) {
            return $command::run($argv);
        }

        return false;
    }

}