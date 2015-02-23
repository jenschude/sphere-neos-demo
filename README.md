# sphere-neos-demo

A Content Commerce technology demo that shows how to create a [Neos](http://neos.typo3.org/) based website with eCommerce functionalities using the [SPHERE.IO](http://dev.sphere.io) eCommerce-as-an-API Platform.

TODO link to a live URL with demo data

## Getting Started (Mac OS X)

First go to the [SPHERE.IO Merchant Center](http://admin.sphere.io/) and create a test project (say yes to test data)

Local technical prerequisites:

 * Virtualbox installed [download](http://www.virtualbox.org/) or `brew install virtualbox`
 * Vagrant installed [download](https://www.vagrantup.com/downloads.html) or or `brew install vagrant vagrant-manager`
 * an SSH shell (Mac OS built-in or the phpStorm integrated one)
 * [composer](http://composer.org/) installed, e.g. `brew install composer`

Steps to start the demo system:

 1. clone this repository into an empty directory
 1. run `vagrant box add XXX` and wait until the Ubuntu VM image that we have prepared for a neos installation.
 1. TODO vagrant init ??
 1. run `vagrant up` and wait until the VM has started
 1. run `vagrant ssh` to get a console in the running VM.

All following commands are meant to be run inside the VM:

 1. TODO
 1.
 1. Open http://xxx-todo-vm-adress)


## B: local environment (without a Vagrant VM)

Install the following software and PHP extensions and tools (replace `brew` command with `apt-get` on debian/Ubuntu):

(alternatively, follow some other documentation on the web, e.g. https://rtcamp.com/tutorials/mac/osx-brew-php-mysql-nginx/ )

(if you get an error concerning lib, do `brew tap homebrew/dupes`)

 * PHP: `brew install php55 --with-fpm --without-apache --with-debug`
 * follow the instructions at the end of the `brew info php55` to start the php-fpm via launchctl (TODO)
     * cp /usr/local/Cellar/php55/5.4.15/homebrew.mxcl.php55.plist ~/Library/LaunchAgents
     * To Start PHP-FPM: 
     * launchctl load -w ~/Library/LaunchAgents/homebrew.mxcl.php55.plist
 * PHP extensions: `brew install php55-igbinary php55-apcu php55-yaml php55-imagick php55-mysqlnd_ms php55-tidy`
 * A database for neos: `brew install mariadb`
 * TODO startet die jetzt schon von selbst? 
 * Nginx as webserver: `brew install nginx`
 * to be safe, do a `brew doctor` and fix stuff

Make sure that

 * the `php.ini` is identical for both the CLI and the FPM environment (esp. enough memory for the CLI and all extensions in the CLI, too). If you install via brew this is usually already the case.
 * Give the `root` mysql/mariadb user a password `mysqladmin -uroot password` (or create a special user) and create a correctly configured database via ``mysql -uroot -p -e "CREATE DATABASE `sphere-neos-demo` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;``
 * add this line to your `/etc/hosts` file: `127.0.0.1 sphere-neos-demo.vmdev`
 * do `composer install`
     * TODO `Class TYPO3\Flow\Composer\InstallerScripts is not autoloadable, can not call post-package-install script`
     * Add your SPHERE.IO project name, client ID and client secret to the `/Configuration/Settings.yaml` file
 * copy `nginx.conf.example` to `nginx.conf` and adjust all four local paths
 * edit your `/usr/local/etc/nginx/nginx.conf` file and add an `include /Users/foo/bar/sphere-neos-demo/nginx.conf;` line in the `http` block. 
 * start `nginx` or restart it with `nginx -s reload`
 * (mac os X) start or restart the php-fpm (until reboot) `launchctl load /usr/local/opt/php55/homebrew.mxcl.php55.plist`
 * Open [http://sphere-neos-demo.vmdev:8080/setup]() and go trough the necessary steps. 

If something went wrong and you had to restart, remember to XXX clear the flow cache.


# TODOS:
 
 * move sphere-php-sdk dependency to Neos.Sphere package composer.json
 * 
