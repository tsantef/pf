# PF - PHPFog Command Line


## Installation

### OSX

Download and install the pf command line tool

    curl -sL http://tinyurl.com/7ccchmg | sudo php


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

Show an apps details.

    pf details [<appname>|<app_id>]

#### Clone

Pull down an app for the first time.

	pf clone [<appname>|<app_id>] [directory]
	
#### Whoami

Shows the current username logged in.

    pf whoami
    
#### Logout

	pf logout
	
	
	