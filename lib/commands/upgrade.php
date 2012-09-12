<?php
function pf_upgrade() {
    chdir(PKG_DIR);
    info("Updating...");
    execute("git pull", $output);
    ewrap($output);

    return true;
}
