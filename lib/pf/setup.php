<?php
function pf_setup($argv) {
    # Check if git is installed
    $has_git = has_bin('git');
    echo wrap('Git Installed? '.($has_git ? 'yes' : 'no'));

    # Check if ssh-kegen is installed
    $has_ssh = has_bin('ssh-keygen');
    echo wrap('SSH Keygen Installed? '.($has_ssh ? 'yes' : 'no'));

    # Test Connection to PHPFog API
    $phpfog = new PHPFogClient();
    try {
        $has_api = $phpfog->login();
    } catch (Exception $e) {
        # Do nothing?
    }
    if (!$has_api) {
        die('Failed to login');
    }

    # Create an ssh key
    $ssh_path = realpath(HOME.".ssh");
    $ssh_key_name = $ssh_path."/".$phpfog->username();
    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", '-', $phpfog->username());
    if (!file_exists($ssh_key_name)) {
        $exit_code = execute("ssh-keygen -q -t rsa -b 2048 -f ".$ssh_key_name);
        if ($exit_code != 0) {
            die('Failed to generate ssh key');
        }
        $fh = fopen($ssh_path."/config", 'a') or die("Can't open file: ".$ssh_path."/config");
        fwrite($fh, wrap("Host ".$ssh_identifier));
        fwrite($fh, wrap("    HostName git01.phpfog.com"));
        fwrite($fh, wrap("    User git"));
        fwrite($fh, wrap("    IdentityFile ".$ssh_key_name));
        fclose($fh);
    }

    # Upload ssh key
    $pubkey = file_get_contents($ssh_key_name.".pub");
    try {
        $phpfog->new_sshkey('', $pubkey);
    } catch(PestJSON_ClientError $e) {}

}

function has_bin($name) {
    $output = null;
	exec("which ".$name, $output);
    $line = trim(current($output));
    unset($output);
	return file_exists($line);
}
?>
