<?php
function pf_setup($argv) {
    define("TAB", "    ");

    # Check if git is installed
    $has_git = has_bin('git');
    if (!$has_git) {
        echo wrap("You need to install git before continuing!");
        echo wrap("After installing git run this setup in a new terminal window.");
        return true;
    }

    # Check if ssh-kegen is installed
    $has_ssh = has_bin('ssh-keygen');
    if (!$has_git) {
        echo wrap("You need to install ssh before continuing!");
        return true;
    }

    # Test Connection to PHPFog API
    $phpfog = new PHPFog();
    try {
        $has_api = $phpfog->login();
    } catch (Exception $e) {
        echo wrap("Something blew up during login!");
    }
    if (!$has_api) {
        die(wrap('Failed to login'));
    }

    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", '-', $phpfog->username());

    # Create an ssh key
    $ssh_path = realpath(HOME.".ssh");
    $ssh_key = $ssh_path.'/'.$ssh_identifier;
    if (!file_exists($ssh_key)) {
        $exit_code = execute("ssh-keygen -q -t rsa -b 2048 -f ".$ssh_key);
        if ($exit_code != 0) {
            die(wrap(red('Failed to generate ssh key')));
        }
    }

    # Add ssh to config
    $ssh_config_path = $ssh_path."/config";
    $config = @file_get_contents($ssh_config_path);
    $config_host_line = "Host ".$ssh_identifier;
    if(!strpos($config, $config_host_line)) {
        $fh = @fopen($ssh_config_path, 'w') or die(wrap("Can't open file: ".$ssh_config_path));
        fwrite($fh, wrap($config_host_line));
        fwrite($fh, wrap(TAB."HostName git01.phpfog.com"));
        fwrite($fh, wrap(TAB."User git"));
        fwrite($fh, wrap(TAB."IdentityFile ".$ssh_key).PHP_EOL);
        fwrite($fh, $config);
        fclose($fh);
    }

    $pubkey = file_get_contents($ssh_key.".pub");

    try {
        $phpfog->new_sshkey('', $pubkey);
        echo wrap(green("Successfully installed ssh key."));
    } catch(PestJSON_ClientError $e) {
        $resp = $phpfog->last_response();
        $body = json_decode($resp['body']);
        $message = $body->message;
        echo wrap("Error: ".red($message));
    }

    echo wrap(bwhite("To clone an app use the following steps:"));
    echo wrap("1. List your apps: ".bwhite("pf list apps"));
    echo wrap("2. To fetch your application code: ".bwhite("pf clone <app_id>"));
    echo wrap("3. Make your changes");
    echo wrap("4. Stage changes in your local repo: ".bwhite("git add -A"));
    echo wrap("5. Commit changes: ".bwhite("git commit -m \"My first commit\""));
    echo wrap("6. Deploy to PHP Fog: ".bwhite("pf push"));
    echo wrap("For more information visit: ".bwhite("http://dev.appfog.com/features/article/pf_command_line_tool"));

    return true;
}

function has_bin($name) {
    $output = null;
	exec("which ".$name, $output);
    $line = trim(current($output));
    unset($output);
	return file_exists($line);
}
?>
