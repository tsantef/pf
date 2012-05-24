<?php
function pf_push($argv) {
    system("git update-index -q --ignore-submodules --refresh");

    # Detect unclean repo
    execute("git status --porcelain", $output);
    if ($output != "") {
        echo wrap("There are uncommited changes.");
        $commit_message = prompt("Enter a commit message to check in changes: ");
        if ($commit_message != "") {
            system("git add -A");
            system("git commit -m \"$commit_message\"");
        } else {
            if (strtolower(prompt("No commit message given, do you want to continue without commiting?[yN]: ")) != 'y') {
                return true;
            }
        }
    }

    # Detect git summodules
    execute("git config --list | grep '^submodule.' | wc -l", $output);
    $has_submodules = intval($output) > 0;

    if (!$has_submodules) {
        # delete local pf-deploy branch
        execute("git branch | grep 'pf-deploy' | wc -l", $output);
        if (intval($output) > 0) {
            execute("git branch -D pf-deploy");
        }

        # delete remote pf-deploy branch
        execute("git branch -r | grep 'pf-deploy' | wc -l", $output);
        if (intval($output) > 0) {
            execute("git push origin :pf-deploy", $ingnore);
        }

        # push
        system("git push");
    } else {
        return pf_deploy();
    }

    return true;
}
?>
