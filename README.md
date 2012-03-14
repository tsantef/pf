# PF - PHPFog Command Line


## Installation

### OSX

Download and install the pf command line tool

    curl -s https://raw.github.com/phpfog/pf/master/bin/installer | sudo php


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
	
	
	