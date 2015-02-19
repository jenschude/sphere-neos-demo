# sphere-neos-demo

A Content Commerce technology demo that shows how to create a [Typo3 neos](http://neos.typo3.org/) based website with eCommerce functionalities using the [SPHERE.IO](http://dev.sphere.io) eCommerce-as-an-API Platform. 

TODO link to a live URL with demo data

## Getting Started (Mac OS X)

First go to the [SPHERE.IO Merchant Center](http://admin.sphere.io/) and create a test project (say yes to test data)

Local technical prerequisites: 

 * Virtualbox installed [download](http://www.virtualbox.org/) or `brew install virtualbox`  
 * Vagrant installed [download](https://www.vagrantup.com/downloads.html) or or `brew install vagrant vagrant-manager`
 * an SSH shell (Mac OS built-in or the phpStorm integrated one)

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

Install the following software and PHP extensions and tools (assuming ubuntu or Mac OS homebrew):
 * `brew install php55 --with-fpm`
 * php55-igbinary
 * php55-apcu
 * php55-yaml
 * php55-imagick
 * php55-mysqlnd_ms
 * php55-tidy
 * mariadb
 * nginx

Make sure that 

 * the php.ini is identical for both the CLI and the FPM environment (esp. enough memory for the CLI and all extensions there). If you install via brew this is usually already the case. 
 * Give the root mysql/mariadb user a password (or create a special user) and create a correctly configured database:
   * "mysqladmin -uroot password"
   * mysql -uroot -p < "CREATE DATABASE sphere-neos-demo DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"
 * XXX include nginx con file of the vhost
 * entry to hosts file: `127.0.0.1 sphere-neos-demo.dev`

Move into the directory you cloned this project to and:
 
* edit the neos configuration file XXX:
  * add the database user and password
  * add your SPHERE.IO project name, client ID and client secret. TODO where to find. 
* TODO 
* Open http://sphere-neos-demo.dev:8080/setup and go trough the necessary steps. 

If something went wrong and you had to restart, remember to XXX clear the flow cache. 