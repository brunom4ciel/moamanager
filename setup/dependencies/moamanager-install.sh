#!/bin/bash


echo "Install script for Latest MOAManager by Bruno Maciel\n"

echo "Install MOAManager\n"

currentpath=$(dirname $(readlink -f $0))

dirmoam_files=$currentpath/../../

dirmoam_web="/var/www/html/moamanager"
dirmoam_data="/var/www/moamanagerdata"
dirmoam_processing="$dirmoam_data/exec/"
dirmoam_storage="$dirmoam_data/storage/"

echo "create folder in path $dirmoam_web"
mkdir -p $dirmoam_web;
chmod 0777 -R $dirmoam_web

echo "create folder in path $dirmoam_data"
mkdir -p $dirmoam_data;
chmod 0777 -R $dirmoam_data

echo "create folder in path $dirmoam_processing"
mkdir -p $dirmoam_processing;
chmod 0777 -R $dirmoam_processing

echo "create folder in path $dirmoam_storage"
mkdir -p $dirmoam_storage;
chmod 0777 -R $dirmoam_storage

cd $dirmoam_files;

cp -rv $dirmoam_files/* $dirmoam_web

# DB Variables
mysqlhost=localhost
mysqldb=moamanager
mysqluser=root
mysqlpass=123

cd $dirmoam_web/core/;

sed -e "s/localhost/"$mysqlhost"/" -e "s/root/"$mysqluser"/" -e "s/123/"$mysqlpass"/" -e "s/moamanagerdb/"$mysqldb"/" properties-sample.php > properties.php

cd "$dirbase1";
#echo $dirbase1;

systemctl enable mysql

# DB Variables
mysqlhost=localhost
mysqldb=moamanager
mysqluser=root
mysqlpass=123

echo "Press [ENTER] only to leave the root user password as 123"

sudo mysql -u$mysqluser -p -e "UPDATE mysql.user SET Password = PASSWORD('"$mysqlpass"') WHERE User = '"$mysqluser"';"

sudo mysql -u$mysqluser -p -e "UPDATE mysql.user SET authentication_string = PASSWORD('"$mysqlpass"') WHERE User = '"$mysqluser"';"

sudo mysql -u$mysqluser -p$mysqlpass -e "FLUSH PRIVILEGES;"

sudo mysql -u$mysqluser -p$mysqlpass -e "DROP DATABASE IF EXISTS $mysqldb;"

sudo mysql -u$mysqluser -p$mysqlpass -e "CREATE DATABASE $mysqldb;"

sudo mysql -u$mysqluser -p$mysqlpass $mysqldb < $currentpath/dump-database.sql

rm -fr $dirmoam_web/setup


