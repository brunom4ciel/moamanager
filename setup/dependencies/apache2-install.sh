#!/bin/bash


# by Bruno Maciel

echo "Install and configuration Apache\n"

sudo apt-get install apache2 -y

sudo service apache2 stop
sudo systemctl stop apache2.service
sudo /etc/init.d/apache2 stop

sudo sed -e "s/<\/VirtualHost>/php_value upload_max_filesize 1000M\nphp_value post_max_size 1000M\nphp_value memory_limit 999M\n\n<\/VirtualHost>/" /etc/apache2/sites-enabled/000-default.conf > /etc/apache2/sites-enabled/000-default-aux.conf

sudo mv /etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.bkp
sudo mv /etc/apache2/sites-enabled/000-default-aux.conf /etc/apache2/sites-enabled/000-default.conf
sudo rm -fr /etc/apache2/sites-enabled/000-default.bkp

sudo service apache2 restart
sudo systemctl restart apache2.service
sudo /etc/init.d/apache2 restart











