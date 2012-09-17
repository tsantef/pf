<?php
function pf_setup($argv) {
    # Check if git is installed
    if (!has_bin('git')) {
        failure("You need to install git before continuing!");
        failure("After installing git run this setup in a new terminal window.");

        return true;
    }

    # Check if ssh-kegen is installed
    if (!has_bin('ssh-keygen')) {
        failure("You need to install ssh before continuing!");

        return true;
    }

    # Test Connection to PHPFog API
    $phpfog = new PHPFog();
    try {
        $has_api = $phpfog->login();
    } catch (PestJSON_Unauthorized $e) {
        failure("Invalid username or password. Please try again.");
        exit(1);
    } catch (Exception $e) {
        failure("Error: {$e->getMessage()}");
        exit(1);
    }
    if (!isset($has_api) || !$has_api) {
        die(failure('Failed to log in.'));
    }

    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", '-', $phpfog->username());

    # Create an ssh key
    $ssh_real_path = HOME."/.ssh/".$ssh_identifier;
    if (!file_exists($ssh_real_path)) {
        $exit_code = execute("ssh-keygen -q -t rsa -b 2048 -f ".$ssh_real_path);
        if (0 != $exit_code) {
            die(failure('Failed to generate ssh key'));
        }
    }

    # Add ssh to config
    $ssh_config_path = HOME."/.ssh/config";
    $config_host_line = "Host ".$ssh_identifier;
    if (strpos(@file_get_contents($ssh_config_path), $config_host_line) === false) {
        $fh = @fopen($ssh_config_path, 'a') or die(failure("Can't open file: ".$ssh_config_path));
        fwrite($fh, PHP_EOL.wrap($config_host_line));
        fwrite($fh, wrap("    HostName git01.phpfog.com"));
        fwrite($fh, wrap("    User git"));
        fwrite($fh, wrap("    IdentityFile ".HOME."/.ssh/".$ssh_identifier).PHP_EOL);
        fclose($fh);
    } else {
        info("Config is already set up.");
    }

    $pubkey = file_get_contents($ssh_real_path.".pub");

    try {
        $phpfog->new_sshkey('', $pubkey);
        success("Successfully installed ssh key.");
    } catch (PestJSON_ClientError $e) {
        $resp = $phpfog->last_response();
        $body = json_decode($resp['body']);
        $message = $body->message;
        failure("Error: ".$message);
    }

    ewrap(bwhite("To clone an app use the following steps:"));
    ewrap("1. List your apps: ".bwhite("pf list apps"));
    ewrap("2. To fetch your application code: ".bwhite("pf clone <app_id>"));
    ewrap("3. Make your changes");
    ewrap("4. Stage changes in your local repo: ".bwhite("git add -A"));
    ewrap("5. Commit changes: ".bwhite("git commit -m 'My first commit'"));
    ewrap("6. Deploy to PHP Fog: ".bwhite("pf push"));
    ewrap("For more information visit: ".bwhite("http://dev.appfog.com/features/article/pf_command_line_tool"));

    return true;
}
