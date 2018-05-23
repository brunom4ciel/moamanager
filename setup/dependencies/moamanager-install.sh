#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel

echo "Install script for Latest MOAManager by Bruno Maciel\n"

echo "Install MOAManager\n"

dirbase1=$(dirname $(readlink -f $0))
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

cd $dirbase1;

echo "create folder in path $dirbase1/moam"
mkdir -p $dirbase1/moam;
chmod 0777 -R $dirbase1/moam

#rm -f $dirbase1/moam

tar zxf $dirbase1/moam.tar.gz -C $dirbase1/moam

#rm moam-install.tar.gz


rm -rf $dirmoam_web/*

mv $dirbase1/moam/* $dirmoam_web


echo "Define the access data to MySQL."


# DB Variables
echo "MySQL Host: localhost"
read mysqlhost
export mysqlhost

echo "MySQL DB Name: moamanager. Alert: DROP DATABASE IF EXISTS db name"
read mysqldb
export mysqldb

echo "MySQL DB User: root"
read mysqluser
export mysqluser

echo "MySQL Password: 123"
read mysqlpass
export mysqlpass

echo "Define the access data to MOAManager."

#echo "Admin fullname:"
#read moamfullname
#export moamfullname

echo "Admin Email:"
read moamemail
export moamemail

echo "Admin Password:"
read moampass
export moampass

# Butcher our wp-config.php file

cd "$dirmoam_web/core/";
echo "$dirmoam_web/core/";

sed -e "s/localhost/"$mysqlhost"/" -e "s/root/"$mysqluser"/" -e "s/123/"$mysqlpass"/" -e "s/moamanagerdb/"$mysqldb"/" properties-sample.php > properties.php

cd "$dirbase1";
#echo $dirbase1;

sed -e "s/brunom4ciel@gmail.com/"$moamemail"/" -e "s/123/"$moampass"/" dump-database.sql > dump-database-tmp.sql

#@Echo off

mysql -u$mysqluser -p$mysqlpass -e "DROP DATABASE IF EXISTS $mysqldb;"

mysql -u$mysqluser -p$mysqlpass -e "create database $mysqldb;"

mysql -u$mysqluser -p$mysqlpass $mysqldb < $dirbase1/dump-database-tmp.sql


rm $dirbase1/dump-database-tmp.sql

rm -rf $dirbase1/moam


