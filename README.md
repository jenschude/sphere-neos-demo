# sphere-neos-demo

A Content Commerce technology demo that shows how to create a [TYPO3 Neos](http://neos.typo3.org/) based website with eCommerce functionalities using the [SPHERE.IO](http://dev.sphere.io) hosted E-Commerce-API.

TODO link to a live URL with demo data

## Getting Started (Mac OSX)

First go to the [SPHERE.IO Merchant Center](http://admin.sphere.io/) and create a test project (agree to insert test data). Make sure to put down your API credentials somewhere because you will need them when connecting SPHERE.IO to TYPO3 Neos.

Local technical prerequisites:

 * Virtualbox installed [download](http://www.virtualbox.org/) or `brew install virtualbox`
 * Vagrant installed [download](https://www.vagrantup.com/downloads.html) or `brew install vagrant vagrant-manager`
 * an SSH shell (Mac OS built-in or the phpStorm integrated one)

Steps to start the demo system:

 1. Install the vagrant plugin hostsupdater for automatically managing the hosts file with vagrant
	`vagrant plugin install vagrant-hostsupdater`
 1. clone this repository into an empty directory
 	`git clone https://github.com/ct-jensschulze/sphere-neos-demo`
 1. switch to the project folder
 	`cd sphere-neos-demo`
 1. download composer if not installed on system
  	`curl -sS https://getcomposer.org/installer | php`
 1. install required packages with composer
 	`php composer.phar install -o` or with composer installed just `composer install -o`
 1. Add SPHERE.IO credentials to Configuration\Settings.yaml
 1. start the vagrant box
	`vagrant up`
 1. login to the vagrant box using `vagrant ssh` and run the setup script
   /var/www/sphere-neos-demo/setup-vagrant.sh

The Frontend is located at

* [http://sphere-neos-demo.vm/](http://sphere-neos-demo.vm/)

The Backend is located at

 * [http://sphere-neos-demo.vm/neos/login](http://sphere-neos-demo.vm/neos/login)

 * User: admin
 * Password: password

For development enable the rsync using `vagrant rsync-auto`

# TODOS:

 * move sphere-php-sdk dependency to Neos.Sphere package composer.json
