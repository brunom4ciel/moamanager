#!/bin/bash

echo "Install PHP 7"



$add-apt-repository ppa:ondrej/php-7.3 -y
add-apt-repository ppa:ondrej/php -y

apt-get update -y
apt-get purge php5-fpm -y

apt-get install php7.3 -y
apt-get install libapache2-mod-php7.3 -y
apt-get install php7.3-cli -y
apt-get install ph7.3p-common  -y
apt-get install php7.3-mbstring -y
apt-get install php7.3-gd -y
apt-get install php7.3-intl -y
apt-get install php7.3-xml -y
apt-get install php7.3-mysql -y
apt-get install php7.3-mcrypt -y 
apt-get install php7.3-zip -y
apt-get install php7.3-curl -y


