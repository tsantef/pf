<?php

function pf_delete($argv) {
    $phpfog = new PHPFog();
    
    $arg = array_shift($argv);

    switch (strtolower($arg)) {
        case "app":
            $raw_app_id = array_shift($argv);

            if ($raw_app_id=="") { return false; }

            $app_id = intval($raw_app_id);

            if ("$app_id" != $raw_app_id) {
                $app_id = $phpfog->get_app_id_by_name($raw_app_id);
                if ($app_id == null) {
                    failure_message("No app found with the name: $raw_app_id".PHP_EOL);
                    return true;
                }
            }

            try {
                $app = $phpfog->delete_app($app_id);
            } catch (PestJSON_NotFound $e) {
                failure_message($phpfog->get_api_error_message().PHP_EOL);
            }

            return true;
        break;

        case "sshkey":
            $sshkey_id = array_shift($argv);

            if ($sshkey_id=="") { return false; }

            try {
                $response = $phpfog->delete_sshkey($sshkey_id);
            } catch (PestJSON_NotFound $e) {
                failure_message($phpfog->get_api_error_message().PHP_EOL);
            }

            return true;
        break;
    }

    return false;
}