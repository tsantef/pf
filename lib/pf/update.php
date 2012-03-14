<?php
namespace pf;

class update {
    static public function run($argv) {
        # Copy repo to temp folder
        $temp_git_folder = temp_folder();
        cp_r(WORKING_DIR, $temp_git_folder);
        chdir($temp_git_folder);

        # Checkout pf-deploy branch
        system("git checkout -b pf-deploy");

        # Get list of submodules
        $submodule_list = "";
        execute("git config -f .gitmodules --get-regexp '^submodule\..*\.path$'", $submodule_list);

        # Submodule sync
        execute("git submodule sync");

        # Remove submodules
        execute("git mv .gitmodules .gitmodules.remove");

        foreach (split("\n", $submodule_list) as $value) {
            preg_match("/^submodule\.(.+?)\.path (.+?)$/", $value, $matches);
            $module_name = $matches[1];
            $path = $matches[2];

            # Delete sub module repo
            rm_rf($path."/.git");

            # Clear git cache
            execute("git rm --cached ".$path);
        }

        # Add changes
        execute("git add .");
        execute("git add -u");

        # Commit changes
        execute('git commit -m "deploy"');

        system("git push --force");

        # Clean up temp folder
        chdir(WORKING_DIR);
        rm_rf($temp_git_folder);

        return true;
    }
}
