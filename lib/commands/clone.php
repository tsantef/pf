<?php
function pf_clone($argv) {
    $phpfog = new PHPFog();

    # Check if setup as been run
    if ($phpfog->username() == '') {
        $phpfog->login();
    }
    $ssh_identifier = preg_replace("/[^A-Za-z0-9-]/", '-', $phpfog->username());
    $config_host_line = "Host ".$ssh_identifier;
    if (!file_exists(HOME."/.ssh/".$ssh_identifier) || false === strpos(@file_get_contents(HOME."/.ssh/config"), $config_host_line)) {
        failure("Missing ssh configuration for your login. Please run pf setup.");
        exit(1);
    }

    $raw_app_id = array_shift($argv);
    $directory = array_shift($argv);

    if (empty($raw_app_id)) {
        # List apps and prompt for one
        $apps = $phpfog->get_apps();
        foreach ($apps as $app) {
            echo_item($app);
        }
        $raw_app_id = prompt("Please provide an app name/id from the list above: ");
    }

    if (!is_numeric($raw_app_id)) {
        $app_id = $phpfog->get_app_id_by_name($raw_app_id);
        if (null == $app_id) {
            failure("No app found with the name: ".$raw_app_id);

            return true;
        }
    } else {
        $app_id = intval($raw_app_id);
    }

    try {
        $app = $phpfog->get_app($app_id);
    } catch (PestJSON_NotFound $e) {
        failure(wrap($phpfog->get_api_error_message()));

        return true;
    }

    $repo_url = $ssh_identifier.':'.$app['git_repo_name'];

    info("git clone ".$repo_url);
    if (execute("git clone ".$repo_url.' '.$directory) > 0) {
        failure("Failed to clone app. Run 'pf setup' to ensure you have your ssh key installed correctly.");
    }

    return true;
}
