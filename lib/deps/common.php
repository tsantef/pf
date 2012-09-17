<?php
define("USECOLOR", usecolor());

function wrap($msg) {
    return $msg.PHP_EOL;
}
function ewrap($msg) {
    echo $msg.PHP_EOL;
}

function success($msg) {
    ewrap(green($msg));
}
function info($msg) {
    ewrap(teal($msg));
}
function warning($msg) {
    ewrap(colorize($msg, 33));
}
function failure($msg) {
    ewrap(red($msg));
}

function usecolor() {
    return (!function_exists("posix_isatty") || !posix_isatty(STDOUT)) ? false : true;
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

function echo_item($obj, $description = null) {
    $name = $obj['name'];
    $id = $obj['id'];
    echo wrap(bwhite($name).(null != $description ? " - ".$description : '')." (ID: ".teal($id).')');
}

function pf_deploy() {
    # Copy repo to temp folder
    $temp_git_folder = temp_folder();
    cp_r(WORKING_DIR, $temp_git_folder);

    $prefix = "cd ".$temp_git_folder." && git ";

    # Checkout pf-deploy branch
    execute($prefix."checkout -b pf-deploy");

    # Get list of submodules
    $submodule_list = '';
    execute($prefix."config -f .gitmodules --get-regexp '^submodule\..*\.path$'", $submodule_list);

    # Submodule sync
    execute($prefix."submodule sync");

    # Remove submodules
    execute($prefix."mv .gitmodules .gitmodules.remove");

    foreach (split("\n", $submodule_list) as $value) {
        preg_match("/^submodule\.(.+?)\.path (.+?)$/", $value, $matches);
        $module_name = $matches[1];
        $path = $matches[2];

        # Delete sub module repo
        rm_rf($temp_git_folder.'/'.$path."/.git");

        # Clear git cache
        execute($prefix."rm --cached ".$path);
    }

    # Add changes
    execute($prefix."add .");
    execute($prefix."add -u");

    # Commit changes
    execute($prefix.'commit -m "deploy"');

    execute($prefix."push origin HEAD --force");

    # Clean up temp folder
    rm_rf($temp_git_folder);

    return true;
}

function fix_path($p) {
    return str_replace('\\', DIRECTORY_SEPARATOR, $p);
}
