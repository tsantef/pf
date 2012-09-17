<?php
function pf_list($argv) {
    $phpfog = new PHPFog();

    switch (strtolower(array_shift($argv))) {
        case "clouds":
            $clouds = $phpfog->get_clouds();
            $clouds[] = array('id' => 0, 'name'=> "Shared");
            foreach ($clouds as $cloud) {
                echo_item($cloud);
            }

            return true;
        case "apps":
            try {
                $raw_cloud_id = array_shift($argv);
                $cloud_id = (strtolower($raw_cloud_id) == 'shared' || $raw_cloud_id == '0') ? 'shared' : intval($raw_cloud_id);
                # null = 0, 0 || shared = shared
                foreach ($phpfog->get_apps($cloud_id) as $app) {
                    echo_item($app, $app['status']);
                }
            } catch (PestJSON_NotFound $e) {
                failure($phpfog->get_api_error_message());
            }

            return true;
        case "sshkeys":
            foreach ($phpfog->get_sshkeys() as $key) {
                echo_item($key);
            }

            return true;
        default:
            return false;
    }
}
