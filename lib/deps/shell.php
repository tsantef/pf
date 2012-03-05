<?php

# Run a shell command
function execute($cmd, &$output=null) {
    $output_array = null;
    $exit_code = null;
    exec($cmd, $output_array, $exit_code);
    if (is_array($output_array)) { 
        $output = join("\n", $output_array);
    }
    return $exit_code;
}

# Create a temporary folder
function temp_folder() {
    $tempfile=tempnam(__FILE__, '');
    $path = realpath($tempfile);
    if (file_exists($tempfile)) {
        unlink($tempfile);
    }
    return $path;
}

# Copy a folder recursively
function cp_r($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src.'/'.$file) ) {
                cp_r($src.'/'.$file, $dst.'/'.$file);
            } else {
                copy($src.'/'.$file, $dst.'/'.$file);
            }
        }
    }
    closedir($dir);
}

# Remove a directory recursively
function rm_rf($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir.'/'.$object) == "dir") {
                    rm_rf($dir.'/'.$object);
                } else {
                    unlink($dir.'/'.$object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

# Prompt for input
function prompt($msg, $pw = false) {
    echo "$msg";
    if ($pw == true) { system('stty -echo'); }
    $input = trim(fgets(fopen('php://stdin', 'r')));
    if ($pw == true) { system('stty echo'); echo PHP_EOL; }
    return $input; 
}

    // function prompt($prompt, $pw = false) {
    //     # If client is using Windows OS
    //     // if (preg_match('/^win/i', PHP_OS)) {
    //     //     $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    //     //     file_put_contents($vbscript, 'wscript.echo(InputBox("'.addslashes($prompt).'", "", "password here"))');
    //     //     $command = "cscript //nologo " . escapeshellarg($vbscript);
    //     //     $password = rtrim(shell_exec($command));
    //     //     unlink($vbscript);
    //     //     return $password;
    //     // } else {
    //     //     # IF *nix-based
    //     //     $command = "/usr/bin/env bash -c 'echo OK'";
    //     //     if (rtrim(shell_exec($command)) !== 'OK') {
    //     //         trigger_error("Can't invoke bash");
    //     //         return;
    //     //     }
    //     //     $command = "/usr/bin/env bash -c 'read -s -p \"".addslashes($prompt)."\" mypassword && echo \$mypassword'";
    //     //     $password = rtrim(shell_exec($command));
    //     //     echo PHP_EOL;
    //     //     return $password;
    //     // }
    // }
