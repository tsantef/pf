<?php
function pf_uninstall($argv) {
    # Read in extra arguments
    $args = join(' ', $argv);

    # Read in config for install_dir and exec_dir locations
    if (file_exists(HOME."/.pfconfig")) {
        $config = json_decode(file_get_contents(HOME."/.pfconfig"), true);
    }
    $install_dir = $config['install_dir'];
    $exec_dir = $config['bin_dir'];
    system("php ".PKG_DIR."/bin/installer -u -i ".$install_dir." -e ".$exec_dir.(!empty($args) ? ' '.$args : ''));

    return true;
}
