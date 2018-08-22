#!/bin/bash


echo "Update script for Latest MOAManager by Bruno Maciel\n"

echo "Update MOAManager\n"

currentpath=$(dirname $(readlink -f $0))

dirmoam_files=$currentpath/../../

dirmoam_web="/var/www/html/moamanager"

cd $dirmoam_files;

sudo chmod 777 -R $dirmoam_web

rm -fr $dirmoam_files/core/properties.php
rm -fr $dirmoam_files/include/defines.php

cp -rv $dirmoam_files/* $dirmoam_web

sudo chmod 777 -R $dirmoam_web

rm -fr $dirmoam_web/setup

cd $currentpath;

sh checking-installed.sh
