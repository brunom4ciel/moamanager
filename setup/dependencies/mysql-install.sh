#!/bin/bash

echo "Install MySQL"

currentpath=$(dirname $(readlink -f $0))

cd $currentpath;

sudo apt-get install mysql-server -y

systemctl enable mysql

# DB Variables
mysqluser=root
mysqlpass=123

sudo mysql -u$mysqluser -p -e "UPDATE mysql.user SET Password = PASSWORD('"$mysqlpass"') WHERE User = '"$mysqluser"';"

sudo mysql -u$mysqluser -p$mysqlpass -e "FLUSH PRIVILEGES;"
