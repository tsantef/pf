<?php

function pf_list($argv) {
    $phpfog = new PHPFog();
    
    $arg = array_shift($argv);

    switch (strtolower($arg)) {
        case "clouds":
            $items = $phpfog->get_clouds();
            $items[] = array('id'=>0, 'name'=> "Shared");
            foreach ($items as $item) {
                echo_item($item['name'], $item['id']);
            }
            return true;
        break;

        case "apps":
            try {
                $raw_cloud_id = array_shift($argv);
                $cloud_id = (strtolower($raw_cloud_id) == 'shared') ? 'shared' : intval($raw_cloud_id);
                $items = $phpfog->get_apps($cloud_id);
                foreach ($items as $item) {
                    echo_item($item['name'], $item['id'], $item['status']);
                }
            } catch (PestJSON_NotFound $e) {
                falure_message($phpfog->get_api_error_message().PHP_EOL);
            }
            return true;
        break;

        case "sshkeys":
            $items = $phpfog->get_sshkeys();
            foreach ($items as $item) {
                echo_item($item['name'], $item['id']);
            }
            return true;
        break;
    }

    return false;
}