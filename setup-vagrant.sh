#!/bin/bash
sudo apt-get update
sudo DEBIAN_FRONTEND=noninteractive apt-get -y install nginx php5-fpm php5-apcu php5-cli php5-imagick php5-gd php5-mysqlnd mariadb-server php5-dev php-pear php5-tidy php5-xdebug graphicsmagick libgraphicsmagick1-dev libyaml-dev
mysqladmin -uroot password password
yes '' | sudo pecl install gmagick-1.1.7RC2 igbinary yaml

sudo cp /var/www/sphere-neos-demo/nginx.conf.example /etc/nginx/sites-available/sphere-neos-demo
cd /etc/nginx/sites-enabled/
sudo ln -s ../sites-available/sphere-neos-demo .
sudo service nginx reload

sudo sed -i.bak '/pre-start/i umask 0002' /etc/init/php5-fpm.conf
sudo usermod -a -G www-data vagrant

sudo cp /var/www/sphere-neos-demo/sphere.ini /etc/php5/mods-available/
cd /etc/php5/fpm/conf.d/
sudo ln -s ../../mods-available/sphere.ini 60-sphere.ini
cd /etc/php5/cli/conf.d/
sudo ln -s ../../mods-available/sphere.ini 60-sphere.ini
sudo service php5-fpm restart

cd /var/www/sphere-neos-demo
./setup-flow.sh
