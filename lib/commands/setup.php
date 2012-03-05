<?php

function pf_setup($argv) {

    # Check if git is installed
    $has_git = has_bin('git');
    if (!$has_git) {
        echo "You need to install git before continuing!".PHP_EOL;
        echo "Download and install git from: http://code.google.com/p/git-osx-installer/".PHP_EOL;
        echo "After installing git run this setup in a new terminal window.".PHP_EOL;
        return true;
    }
    
    # Check if ssh-kegen is installed
    $has_ssh = has_bin('ssh-keygen');
    if (!$has_git) {
        echo "You need to install ssh before continuing!".PHP_EOL;
        return true;
    }

    # Test Connection to PHPFog API
    $phpfog = new PHPFog();
    try {
        $has_api = $phpfog->login();
    } catch (Exception $e) {}
    if (!$has_api) { die('Failed to login'.PHP_EOL); }	

    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", "-", $phpfog->username());

    # Create an ssh key
    $ssh_path = realpath(HOME.".ssh");
    $ssh_key_name = "~/.ssh/$ssh_identifier";
    $ssh_real_path = realpath(HOME.".ssh/$ssh_identifier");
    if (!file_exists($ssh_real_path)) {
        $exit_code = execute("ssh-keygen -q -t rsa -b 2048 -f $ssh_key_name");
        if ($exit_code != 0) {
            die('Failed to generate ssh key');
        }
        $fh = fopen("$ssh_path/config", 'a') or die("can't open file".PHP_EOL);    
        fwrite($fh,"Host $ssh_identifier".PHP_EOL);
        fwrite($fh,"    HostName git01.phpfog.com".PHP_EOL);
        fwrite($fh,"    User git".PHP_EOL);
        fwrite($fh,"    IdentityFile $ssh_key_name".PHP_EOL);
        fclose($fh);
    }

    $pubkey = file_get_contents($ssh_real_path.".pub");

    try {
        $phpfog->new_sshkey("", $pubkey);
        echo "Successfully installed ssh key.".PHP_EOL;
    } catch(PestJSON_ClientError $e) {
        $resp = $phpfog->last_response();
        $body = json_decode($resp['body']);
        $message = $body->message;
        echo "Error: $message".PHP_EOL;
    }

}

function has_bin($name) {
    $output=null;
	exec("which $name", $output);
    $line = trim(current($output));
    unset($output);
	return file_exists($line);
}

?>
