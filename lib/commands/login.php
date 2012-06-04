<?php
function pf_login($argv) {
    $phpfog = new PHPFog(false);
    $username = array_shift($argv);

    if ($phpfog->username() == $username) {
        info_message("Already logged in as {$phpfog->username()}.");

        return true;
    }

    if ($phpfog->switch_user($username)) {
        info_message("Switched to {$phpfog->username()}.");

        return true;
    }

    try {
        $has_api = $phpfog->login($username);
    } catch (PestJSON_Unauthorized $e) {
        failure_message("Invalid login or password. Please try again.");
        exit(1);
    } catch (Exception $e) {
        failure_message("Error: ".$e->getMessage());
        exit(1);
    }
    if (isset($has_api) && $has_api) {
        success_message("Logged in as {$phpfog->username()}");

        return true;
    } else {
        die(wrap(red('Failed to login')));
    }

}
