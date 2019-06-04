#!/bin/bash

echo "Install PHP 7"

$sudo add-apt-repository ppa:ondrej/php-7.3 -y
sudo add-apt-repository ppa:ondrej/php -y

sudo apt-get update -y
sudo apt-get purge php5-fpm -y

sudo apt-get install php7.3 -y
sudo apt-get install libapache2-mod-php7.3 -y
sudo apt-get install php7.3-cli -y
sudo apt-get install ph7.3p-common  -y
sudo apt-get install php7.3-mbstring -y
sudo apt-get install php7.3-gd -y
sudo apt-get install php7.3-intl -y
sudo apt-get install php7.3-xml -y
sudo apt-get install php7.3-mysql -y
sudo apt-get install php7.3-mcrypt -y 
sudo apt-get install php7.3-zip -y
sudo apt-get install php7.3-curl -y


