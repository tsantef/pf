<?php

function pf_clone($argv) {
    $phpfog = new PHPFog();

    $raw_app_id = array_shift($argv);
    $directory = array_shift($argv);

    if ($raw_app_id=="") { return false; }

    $app_id = intval($raw_app_id);

    if ("$app_id" != $raw_app_id) {
        $app_id = $phpfog->get_app_id_by_name($raw_app_id);
        if ($app_id == null) {
            falure_message("No app found with the name: $raw_app_id".PHP_EOL);
            return true;
        }
    }

    try {
        $app = $phpfog->get_app($app_id);
    } catch (PestJSON_NotFound $e) {
        falure_message($phpfog->get_api_error_message().PHP_EOL);
        return true;
    }

    $git_url = $app['git_url'];
    
    execute("git clone $git_url $directory");

    return true;
}