<?php
function pf_list($argv) {
    $phpfog = new PHPFog();

    switch (strtolower(array_shift($argv))) {
        case "clouds":
            $items = $phpfog->get_clouds();
            $items[] = array('id'=>0, 'name'=> "Shared");
            foreach ($items as $item) {
                echo_item($item['name'], $item['id']);
            }

            return true;
        case "apps":
            try {
                $raw_cloud_id = array_shift($argv);
                $cloud_id = (strtolower($raw_cloud_id) == 'shared' || $raw_cloud_id == '0') ? 'shared' : intval($raw_cloud_id);
                foreach ($phpfog->get_apps($cloud_id) as $item) {
                    echo_item($item['name'], $item['id'], $item['status']);
                }
            } catch (PestJSON_NotFound $e) {
                failure_message($phpfog->get_api_error_message());
            }

            return true;
        case "sshkeys":
            foreach ($phpfog->get_sshkeys() as $item) {
                echo_item($item['name'], $item['id']);
            }

            return true;
        default:
            return false;
    }
}
