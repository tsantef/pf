<?php

function pf_clone($argv) {

    $phpfog = new PHPFog();

    # Check if setup as been run
    if ($phpfog->username() == "") $phpfog->login();
    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", '-', $phpfog->username());
    $ssh_real_path = str_replace("/", DS, HOME.".ssh/".$ssh_identifier);
    $ssh_path = realpath(HOME.".ssh");
    $ssh_config_path = $ssh_path."/config";
    $config_host_line = "Host ".$ssh_identifier;
    $config = @file_get_contents($ssh_config_path);
    if (!file_exists($ssh_real_path) || strpos($config, $config_host_line) === false) {
        failure_message("Missing ssh configuration for your login. Please run pf setup.");
        exit(1);
    }

    $raw_app_id = array_shift($argv);
    $directory = array_shift($argv);

    if (strlen($raw_app_id) == 0) {
        return false;
    }

    $app_id = intval($raw_app_id);

    if (!is_numeric($raw_app_id)) {
        $app_id = $phpfog->get_app_id_by_name($raw_app_id);
        if ($app_id == null) {
            failure_message("No app found with the name: ".$raw_app_id);
            return true;
        }
    }

    try {
        $app = $phpfog->get_app($app_id);
    } catch (PestJSON_NotFound $e) {
        failure_message(wrap($phpfog->get_api_error_message()));
        return true;
    }

    $repo_url = $ssh_identifier.":".$app['git_repo_name'];

    echo wrap("git clone ".$repo_url);
    if (execute("git clone ".$repo_url.' '.$directory) > 0) {
        echo failure_message("Failed to clone app. Run 'pf setup' to insure you have your ssk key installed correctly.");
    }

    return true;
}
?>
