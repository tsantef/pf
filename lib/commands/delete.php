<?php
function pf_delete($argv) {
    $phpfog = new PHPFog();

    switch (strtolower(array_shift($argv))) {
        case "app":
            $raw_app_id = array_shift($argv);

            if (empty($raw_app_id)) {
                return false;
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
                $app = $phpfog->delete_app($app_id);
            } catch (PestJSON_NotFound $e) {
                failure($phpfog->get_api_error_message());
            }

            return true;
        case "sshkey":
            $sshkey_id = array_shift($argv);

            if (empty($sshkey_id)) {
                return false;
            }

            try {
                $response = $phpfog->delete_sshkey($sshkey_id);
            } catch (PestJSON_NotFound $e) {
                failure($phpfog->get_api_error_message());
            }

            return true;
        default:
            return false;
    }
}
