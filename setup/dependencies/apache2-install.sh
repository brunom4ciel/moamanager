#!/bin/bash


# by Bruno Maciel

echo "Install and configuration Apache\n"

apt-get install apache2 -y

service apache2 stop
systemctl stop apache2.service
/etc/init.d/apache2 stop

sed -e "s/<\/VirtualHost>/php_value upload_max_filesize 1000M\nphp_value post_max_size 1000M\nphp_value memory_limit 999M\n\n<\/VirtualHost>/" /etc/apache2/sites-enabled/000-default.conf > /etc/apache2/sites-enabled/000-default-aux.conf

mv /etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.bkp
mv /etc/apache2/sites-enabled/000-default-aux.conf /etc/apache2/sites-enabled/000-default.conf
rm -fr /etc/apache2/sites-enabled/000-default.bkp

service apache2 restart
systemctl restart apache2.service
/etc/init.d/apache2 restart











