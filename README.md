# PF-CLI - PHP Fog Command Line

## Installation

#### Requirements

* PHP-CLI
* curl
* git

### OSX (Lion)

Download and install the pf command line tool

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer | php

#### Troubleshoot OSX Installation

Missing Requirement: **Cannot find git executable.** 

1. Download and install git here: <a href="http://code.google.com/p/git-osx-installer/">http://code.google.com/p/git-osx-installer/</a>
2. Open a new terminal window and run the curl installer again.

### Ubuntu (10.04.4-desktop-amd64)

Download and install the pf command line tool

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer | php
    
#### Troubleshoot Ubuntu Installation

Error: **The program 'curl' is currently not installed.** You can install curl by typing:

    sudo apt-get install curl

Error: **sudo: php: command not found.** You can install PHP-CLI by typing: 

    sudo apt-get install php5-cli

Missing Requirement: **The curl extension for php is not loaded.** You can install the php curl extension by typing:

    sudo apt-get install php5-curl

Missing Requirement: **Cannot find git executable.** You can install git by typing:

    sudo apt-get install git-core

### Windows

Comming soon.



## Usage

### Commands

#### Setup

Creats and uploads a public ssh key.

    pf setup

#### Update

Deploys an app using git submodules.

	pf update

#### List	

Lists clouds, apps, and sshkeys.

	pf list [clouds|apps <cloud_id>|sshkeys]
	
#### Details

Shows an apps details.

    pf details [<appname>|<app_id>]

#### Clone

Pull down an app for the first time.

	pf clone [<appname>|<app_id>] [directory]

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

	pf delete [app|sshkey] [<appname>|<app_id>|<ssh_key_id>]
	
#### Whoami

Shows the current username logged in.

    pf whoami
    
#### Logout

	pf logout
	
	
	