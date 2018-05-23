#!/bin/bash

echo "Install PHP 7"

sudo add-apt-repository ppa:ondrej/php-7.0 -y
sudo apt-get update -y
sudo apt-get purge php5-fpm -y
sudo apt-get install php -y
sudo apt-get install libapache2-mod-php -y
sudo apt-get install php-cli -y
sudo apt-get install php-common  -y
sudo apt-get install php-mbstring -y
sudo apt-get install php-gd -y
sudo apt-get install php-intl 
sudo apt-get install php-xml -y
sudo apt-get install php-mysql -y
sudo apt-get install php-mcrypt -y 
sudo apt-get install php-zip -y
sudo apt-get install php-curl -y


