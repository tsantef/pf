<?php
function pf_delete($argv) {
    $phpfog = new PHPFog();

    switch (strtolower(array_shift($argv))) {
        case "app":
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
                $app = $phpfog->delete_app($app_id);
            } catch (PestJSON_NotFound $e) {
                failure_message($phpfog->get_api_error_message());
            }

            return true;
        case "sshkey":
            $sshkey_id = array_shift($argv);

            if (strlen($sshkey_id) == 0) {
                return false;
            }

            try {
                $response = $phpfog->delete_sshkey($sshkey_id);
            } catch (PestJSON_NotFound $e) {
                failure_message($phpfog->get_api_error_message());
            }
            return true;
        default:
            return false;
    }
}
?>
