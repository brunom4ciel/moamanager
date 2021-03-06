#!/bin/bash


echo "Install script for Latest MOAManager by Bruno Maciel\n"

echo "Install MOAManager\n"

currentpath=$(dirname $(readlink -f $0))

dirmoam_files=$currentpath/../../

dirmoam_web="/var/www/html/moamanager"
dirmoam_data="/var/www/moamanagerdata"
dirmoam_processing="$dirmoam_data/exec/"
dirmoam_workspace="$dirmoam_data/workspace/"

echo "create folder in path $dirmoam_web"
mkdir -p $dirmoam_web
#chmod 0777 -R $dirmoam_web

echo "create folder in path $dirmoam_data"
mkdir -p $dirmoam_data
#chmod 0777 -R $dirmoam_data

echo "create folder in path $dirmoam_processing"
mkdir -p $dirmoam_processing
#chmod 0777 -R $dirmoam_processing

echo "create folder in path $dirmoam_workspace"
mkdir -p $dirmoam_workspace
#chmod 0777 -R $dirmoam_workspace

mkdir -p $dirmoam_processing/brunom4ciel@gmail.com

mkdir -p $dirmoam_workspace/brunom4ciel@gmail.com
mkdir -p $dirmoam_workspace/brunom4ciel@gmail.com/scripts
mkdir -p $dirmoam_workspace/brunom4ciel@gmail.com/trash
mkdir -p $dirmoam_workspace/brunom4ciel@gmail.com/backup

chmod 777 -R $dirmoam_data

cd $dirmoam_files;

cp -rv $dirmoam_files/* $dirmoam_web

# DB Variables
mysqlhost=localhost
mysqldb=moamanager
mysqluser=root
mysqlpass=123

#cd $dirmoam_web/core/;
#sed -e "s/localhost/"$mysqlhost"/" -e "s/root/"$mysqluser"/" -e "s/123/"$mysqlpass"/" -e "s/moamanagerdb/"$mysqldb"/" properties-sample.php > properties.php

systemctl enable mysql


echo "Press [ENTER] only to leave the root user password as 123"

mysql -u$mysqluser -p -e "UPDATE mysql.user SET authentication_string=PASSWORD('123'), plugin='mysql_native_password' WHERE User='root';FLUSH PRIVILEGES;"

#sudo mysql -u$mysqluser -p -e "UPDATE mysql.user SET Password = PASSWORD('$mysqlpass') WHERE User = '$mysqluser';"
#sudo mysql -u$mysqluser -p -e "UPDATE mysql.user SET authentication_string = PASSWORD('$mysqlpass') WHERE User = '$mysqluser';"
#sudo mysql -u$mysqluser -p$mysqlpass -e "FLUSH PRIVILEGES;"

mysql -u$mysqluser -p$mysqlpass -e "DROP DATABASE IF EXISTS $mysqldb;"

mysql -u$mysqluser -p$mysqlpass -e "CREATE DATABASE $mysqldb;"

mysql -u$mysqluser -p$mysqlpass $mysqldb < $currentpath/dump-database.sql

rm -fr $dirmoam_web/setup


