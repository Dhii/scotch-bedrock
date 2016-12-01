# Scotch Bedrock
[![Join the chat at https://gitter.im/Dhii/scotch-bedrock](https://badges.gitter.im/Dhii/scotch-bedrock.svg)](https://gitter.im/Dhii/scotch-bedrock?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

You can use this as a boilerplate to get new development projects up and running in seconds.
After the environment is created, you can remove some provisioning steps, such as the installation of Bedrock:
t has its own `composer.json` file, which you may want to edit, or commit the whole `app` directory.
owever, please note that installation of Bedrock and WordPress will be skipped if they are already found -
see "Provisioning" section for more details.

This is a Vagrant setup with:

* [ScotchBox](http://box.scotch.io)
* [Roots Bedrock](https://roots.io/bedrock/)
* Some simple provision script setup in the Vagrantfile

The reason for this repo is to get a lighter setup than with [Trellis](https://roots.io/trellis/).

## Installation

### Step 1
Download, fork or clone this repo to your local machine, e.g.
```
git clone https://github.com/Dhii/scotch-bedrock thenewproject 
```

### Step 2
Change settings in the two lines of Vagrantfile, e.g. 
```
hostname = "yourhostname"
ip = "192.168.33.111"
```

### Step 3
Run Vagrant! 
```
vagrant up
```

### Step 4
Browse to `http://yourhostname.dev`. The exact URL will be output at the end of installation process.

### Step 5
Log in to WordPress backend with username "admin" and password "admin" at `http://yourhostname.dev/wp/wp-admin`.

## Provisioning

Here's what happens during provisioning:

1. Composer is updated to the latest version.

    Its own update mechanism is used.

2. If WP CLI is not present, it is installed.

    If present, it is updated to the latest version using its own update mechanism.  
    This is important, because some older versions of WP CLI cause fatal PHP errors when installing WP.

3. If Bedrock is not present, it is downloaded using Git from its repo, and installed into the root of the project.

    Bedrock is determined to be installed if the `wp` or `app` folders in the `web` directory (docroot) don't exist.
    The `.git` folder is removed before installation, so it wont confict with one that may exist there.  

4. Dependencies are installed using Composer.

    Dependencies will not be updated, so locked versions will be installed. With the stock Vagrantfile, the only dependencies that will be installed are those in Bedrock's `composer.json` file.  
    At this stage, Composer will use a `.composer-cache` folder local to the project. This is there to decrease provisioning time if it needs to be done multiple times. This is only effective during Vagrant provisioning, and when you are in the box, the default cache directory will be used by Composer.

5. If the server virtualhost config is not present, it is created.

    The default Apache config is used as a template, and default values are replaced in it with actual ones.

6. The virtual host is activated.

    If the vhost is already active, this will just display a message.  
    The server will be restarted regardless.

7. If WordPress is not installed, installation is performed.

    WordPress is determined to be installed if Bedrock's `.env` file is present.
    Installation is performed using WP CLI.

8. Cleanup is performed

    Right now, nothing is being done at this stage.
