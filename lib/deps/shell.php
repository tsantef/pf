<?php

# Run a shell command
function execute($cmd, &$output = null) {
    exec($cmd, $output_array = null, $exit_code = null);
    $output = join("\n", $output_array);
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
