<?php
define("USECOLOR", usecolor());

function wrap($msg) {
    return $msg.PHP_EOL;
}

function success_message($message) {
    echo wrap(colorize($message, 32));
}
function info_message($message) {
    echo wrap(colorize($message, 36));
}
function failure_message($message) {
    echo wrap(colorize($message, 31));
}

function echo_item($name, $id, $description = null) {
    echo ($description != null) ? wrap(bwhite($name)." - $description (ID: ".teal($id).")") : wrap(bwhite($name)." (ID: ".teal($id).")");
}

function usecolor() {
    if (!function_exists("posix_isatty")) {
        return false;
    }
    if (!posix_isatty(STDOUT)) {
        return false;
    }
    return true;
}

function colorize($str, $color) {
    return USECOLOR ? sprintf("\033[0;".$color."m%s\033[0m", $str) : $str;
}

function bwhite($str) {
    return colorize($str, "1;37");
}

function teal($str) {
    return colorize($str, "36");
}

function red($str) {
    return colorize($str, "31");
}

function green($str) {
    return colorize($str, "32");
}

function pf_deploy() {
    # Copy repo to temp folder
    $temp_git_folder = temp_folder();
    cp_r(WORKING_DIR, $temp_git_folder);

    $prefix = "cd ".$temp_git_folder." && ";

    # Checkout pf-deploy branch
    execute($prefix."git checkout -b pf-deploy");

    # Get list of submodules
    $submodule_list = "";
    execute($prefix."git config -f .gitmodules --get-regexp '^submodule\..*\.path$'", $submodule_list);

    # Submodule sync
    execute($prefix."git submodule sync");

    # Remove submodules
    execute($prefix."git mv .gitmodules .gitmodules.remove");

    foreach (split("\n", $submodule_list) as $value) {
        preg_match("/^submodule\.(.+?)\.path (.+?)$/", $value, $matches);
        $module_name = $matches[1];
        $path = $matches[2];

        # Delete sub module repo
        rm_rf($temp_git_folder.'/'.$path."/.git");

        # Clear git cache
        execute($prefix."git rm --cached ".$path);
    }

    # Add changes
    execute($prefix."git add .");
    execute($prefix."git add -u");

    # Commit changes
    execute($prefix.'git commit -m "deploy"');

    execute($prefix."git push origin HEAD --force");

    # Clean up temp folder
    rm_rf($temp_git_folder);

    return true;
}
