<?php
function pf_login($argv) {
    $phpfog = new PHPFog();
    $username = array_shift($argv);

    # Prompt for username
    if (empty($username)) {
        $username = trim(prompt("PHP Fog Username: "));
    }

    if ($phpfog->username() == $username) {
        info("Already logged in as {$phpfog->username()}.");

        return true;
    }

    if ($phpfog->switch_user($username)) {
        info("Switched to {$phpfog->username()}.");

        return true;
    }

    try {
        $has_api = $phpfog->login($username);
    } catch (PestJSON_Unauthorized $e) {
        failure("Invalid username or password. Please try again.");

        exit(1);
    } catch (Exception $e) {
        failure("Error: {$e->getMessage()}");

        exit(1);
    }
    if (isset($has_api) && $has_api) {
        success("Logged in as {$phpfog->username()}.");

        return true;
    } else {
        die(failure('Failed to log in.'));
    }

}
