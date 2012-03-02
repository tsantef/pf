<?php

function pf_update($argv) {
    
    # Copy repo to temp folder
    $temp_git_folder = temp_folder();
    cp_r(WORKING_DIR, $temp_git_folder);

    $prefix = "cd $temp_git_folder && ";

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
        rm_rf("$temp_git_folder/$path/.git");

        # Clear git cache
        execute($prefix."git rm --cached $path");
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

?>
