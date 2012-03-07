<?php
function pf_details($argv) {
    $phpfog = new PHPFog();

    $raw_app_id = array_shift($argv);

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
        failure_message($phpfog->get_api_error_message());
        return true;
    }

    echo wrap("App Name: ".bwhite($app['name']));
    echo wrap("Id: ".teal($app['id']));
    echo wrap("Url: ".bwhite($app['domain_name']));
    echo wrap("Status: ".bwhite($app['status']));
    echo wrap("Git Url: ".bwhite($app['git_url']));
    echo wrap("DB Host: ".bwhite($app['db_host']));
    echo wrap("DB Name: ".bwhite($app['db_name']));
    return true;
}
?>
