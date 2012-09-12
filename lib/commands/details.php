<?php
function pf_details($argv) {
    $phpfog = new PHPFog();

    $raw_app_id = array_shift($argv);

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
            failure("No app found with the name: ".teal($raw_app_id));

            return true;
        }
    } else {
        $app_id = intval($raw_app_id);
    }

    try {
        $app = $phpfog->get_app($app_id);
    } catch (PestJSON_NotFound $e) {
        failure($phpfog->get_api_error_message());

        return true;
    }

    ewrap("App Name: ".bwhite($app['name']));
    ewrap("Id: ".teal($app['id']));
    ewrap("Url: ".bwhite($app['domain_name']));
    ewrap("Status: ".bwhite($app['status']));
    ewrap("Git Url: ".bwhite($app['git_url']));
    ewrap("DB Host: ".bwhite($app['db_host']));
    ewrap("DB Name: ".bwhite($app['db_name']));

    return true;
}
