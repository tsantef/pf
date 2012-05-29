<?php
function pf_upgrade() {
    exec("php ".dirname(__FILE__)."/../../bin/installer -- --update");
    return true;
}
