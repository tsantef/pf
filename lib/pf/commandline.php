<?php 

class CommandLine {
	
	static public function run($argv) {
		print_r ($argv);

		if ($argv == null || !is_array($argv) || count($argv) < 1) {
			echo "Invalid Command";
			return false;
		}

		$command = array_shift($argv);
		$command = str_replace("-","_",$command);

		echo $command;

		load_lib("commands/$command.php");

		command_list::run($argv);

		call_user_func(array("command_$command", "run", $argv));

		echo "RUN";
	}

}