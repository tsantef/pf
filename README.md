# PF-CLI - PHP Fog Command Line Interface

## Installation

#### Requirements

* PHP-CLI
* Curl
* Curl extension for PHP
* Git

#### Installer options

You can view all the installer options with the following command:
    curl -s https://raw.github.com/phpfog/pf/master/bin/installer | php -- -h

### OSX (10.5+)

Download and install the pf command line tool

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer > installer && php installer

#### Troubleshoot OSX Installation

Missing Requirement: **Cannot find git executable.**

1. Download and install git here: <a href="http://code.google.com/p/git-osx-installer/">http://code.google.com/p/git-osx-installer/</a>
2. Open a new terminal window and run the curl installer again.


### Ubuntu (10.04.4-desktop-amd64)

Download and install the pf command line tool

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer > installer && php installer


#### Troubleshoot Ubuntu Installation

Error: **The program 'curl' is currently not installed.** You can install curl by typing:

    sudo apt-get install curl

Error: **sudo: php: command not found.** You can install PHP-CLI by typing:

    sudo apt-get install php5-cli

Missing Requirement: **The curl extension for php is not loaded.** You can install the php curl extension by typing:

    sudo apt-get install php5-curl

Missing Requirement: **Cannot find git executable.** You can install git by typing:

    sudo apt-get install git-core


### Windows (with XAMPP, Git for Windows, and Git Bash)

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer > installer && php installer

#### Troubleshoot Windows Installation

Error: **sh.exe": php: command not found**

Cause: The curl extension for PHP is not loaded.

Fix: uncomment out the curl extension in the php.ini

    extension=php_curl.dll


## Uninstall

	pf uninstall


## Usage

### Commands

#### Setup

Creates and uploads a public ssh key.

    pf setup

#### List

Lists clouds, apps, and sshkeys.

	pf list (clouds | apps [cloud_id] | sshkeys)

#### Details

Shows an app's details.

    pf details [<appname> | <app_id>]

#### Clone

Pull down an app for the first time.

	pf clone [<appname> | <app_id>] [directory]

#### Pull

Wrapper for git pull.

	pf pull

#### Push

Wrapper for git push.

	pf push

#### Update

Deploys an app using git submodules.

	pf update

#### Delete

Deletes a remote app or remote ssh key.

	pf delete (app (<appname> | <app_id>) | sshkey <ssh_key_id>)

#### Whoami

Shows the current username logged in.

    pf whoami

#### Login

	pf login [username]

#### Logout

	pf logout [username]



