#!/bin/bash
mysql -uroot -ppassword -e "DROP DATABASE sphere_neos_demo; CREATE DATABASE sphere_neos_demo DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;"
mysql -uroot -ppassword -e "DROP DATABASE sphere_neos_demo_dev; CREATE DATABASE sphere_neos_demo_dev DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;"

cd /var/www/sphere-neos-demo
sudo ./flow core:setfilepermissions vagrant www-data www-data

sudo -u www-data ./flow flow:core:compile
sudo -u www-data ./flow doctrine:migrate
sudo -u www-data ./flow site:import --package-key Sphere.Neos.DemoSite
sudo -u www-data ./flow user:create admin password John Doe --roles TYPO3.Neos:Administrator
sudo -u www-data ./flow resource:publish
sudo -u www-data ./flow flow:cache:flush

sudo -u www-data FLOW_CONTEXT=Production ./flow flow:core:compile
sudo -u www-data FLOW_CONTEXT=Production ./flow doctrine:migrate
sudo -u www-data FLOW_CONTEXT=Production ./flow site:import --package-key Sphere.Neos.DemoSite
sudo -u www-data FLOW_CONTEXT=Production ./flow user:create admin password John Doe --roles TYPO3.Neos:Administrator
sudo -u www-data FLOW_CONTEXT=Production ./flow resource:publish
sudo -u www-data FLOW_CONTEXT=Production ./flow flow:cache:flush
