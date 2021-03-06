<?php
# Run a shell command
function execute($cmd, &$output = null) {
    exec($cmd, $output_array, $exit_code);
    if (is_array($output_array)) {
        $output = join(PHP_EOL, $output_array);
    }

    return $exit_code;
}

# Create a temporary folder
function temp_folder() {
    $tempfile = tempnam(__FILE__, '');
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
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src.'/'.$file)) {
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
        foreach ($objects = scandir($dir) as $object) {
            if ($object != '.' && $object != '..') {
                if (is_dir($dir.'/'.$object)) {
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
    echo $msg;
    if (NOT_WIN && $pw) {
        system("stty -echo");
    }
    $input = trim(fgets(fopen("php://stdin", "r")));
    if (NOT_WIN && $pw) {
        system("stty echo");
        echo PHP_EOL;
    }

    return $input;
}

function has_bin($name) {
    $exit_code;
    if (NOT_WIN) {
        $exit_code = execute("which ".$name);
    } else {
        $exit_code = execute("where /Q ".$name);
    }

    return (0 === $exit_code) ? 1 : 0;
}
