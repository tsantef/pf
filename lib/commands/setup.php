<?php

function pf_setup($argv) {

    # Check if git is installed
    $has_git = has_bin('git');
    echo 'Git Installed? '.($has_git?'yes':'no').PHP_EOL;
    
    # Check if ssh-kegen is installed
    $has_ssh = has_bin('ssh-keygen');
    echo 'SSH Keygen Installed? '.($has_ssh?'yes':'no').PHP_EOL;

    # Test Connection to PHPFog API
    $phpfog = new PHPFog();
    try {
        $has_api = $phpfog->login();
    } catch (Exception $e) {}
    if (!$has_api) { die('Failed to login'); }	

    # Create an ssh key
    $ssh_path = realpath(HOME.".ssh");
    $ssh_key_name = $ssh_path."/".$phpfog->username();
    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", "-", $phpfog->username());
    if (!file_exists($ssh_key_name)) {
        $exit_code = execute("ssh-keygen -q -t rsa -b 2048 -f $ssh_key_name");
        if ($exit_code != 0) {
            die('Failed to generate ssh key');
        }
        $fh = fopen("$ssh_path/config", 'a') or die("can't open file");    
        fwrite($fh,"Host $ssh_identifier".PHP_EOL);
        fwrite($fh,"    HostName git01.phpfog.com".PHP_EOL);
        fwrite($fh,"    User git".PHP_EOL);
        fwrite($fh,"    IdentityFile $ssh_key_name".PHP_EOL);
        fclose($fh);
    }

    $pubkey = file_get_contents($ssh_key_name.".pub");

    try {
        $phpfog->new_sshkey("", $pubkey);
    } catch(PestJSON_ClientError $e) {
        var_dump ($phpfog->last_response());
    }
    
    # Upload ssh key
}

function has_bin($name) {
    $output=null;
	exec("which $name", $output);
    $line = trim(current($output));
    unset($output);
	return file_exists($line);
}

?>
