<?php

function pf_details($argv) {
    $phpfog = new PHPFog();

    $raw_app_id = array_shift($argv);

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
    
    echo "App Name: ".bwhite($app['name']).PHP_EOL;
    echo "Id: ".teal($app['id']).PHP_EOL;
    echo "Url: ".bwhite($app['domain_name']).PHP_EOL;
    echo "Status: ".bwhite($app['status']).PHP_EOL;
    echo "Git Url: ".bwhite($app['git_url']).PHP_EOL;
    echo "DB Host: ".bwhite($app['db_host']).PHP_EOL;
    echo "DB Name: ".bwhite($app['db_name']).PHP_EOL;

    return true;
}