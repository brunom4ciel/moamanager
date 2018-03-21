#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel

echo "Install script for Latest MOAManager by Bruno Maciel\n"
echo "install dependencies\n"

echo "Install JAVA"

sudo apt-get install ppa-purge -y
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:webupd8team/java
sudo apt-get update
sudo apt-get install oracle-java8-installer
sudo apt-get install oracle-java8-set-default


echo "Install Apache2"
sudo apt-get install apache2

echo "Install CURL"
sudo apt-get install curl

echo "Install MySQL"
sudo apt-get install mysql-server

echo "Install PHP 7"
sudo add-apt-repository ppa:ondrej/php-7.0
sudo apt-get update
sudo apt-get purge php5-fpm
sudo apt-get install php7.0 libapache2-mod-php7.0 php7.0-cli php7.0-common php7.0-mbstring php7.0-gd php7.0-intl php7.0-xml php7.0-mysql php7.0-mcrypt php7.0-zip php7.0-curl


echo "Define the access data to MySQL."

# DB Variables
echo "MySQL Host:"
read mysqlhost
export mysqlhost

echo "MySQL DB Name:"
read mysqldb
export mysqldb

echo "MySQL DB User:"
read mysqluser
export mysqluser

echo "MySQL Password:"
read mysqlpass
export mysqlpass


echo "Admin fullname:"
read moamuser
export moamuser

echo "Admin Email"
read moamemail
export moamemail

echo "Admin Password:"
read moampass
export moampass




