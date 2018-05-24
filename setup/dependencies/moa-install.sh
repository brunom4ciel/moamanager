#!/bin/bash

echo "Install script for Latest MOA by Bruno Maciel\n"

echo "Install MOA\n"

currentpath=$(dirname $(readlink -f $0))

dir_opt="/opt"
dir_opt_moamanager="$dir_opt/moamanager"
dir_opt_moamanager_moa="$dir_opt_moamanager/moa"

echo "create folder in path $dir_opt_moamanager"
mkdir -p $dir_opt_moamanager;

echo "create folder in path $dir_opt_moamanager_moa"
mkdir -p $dir_opt_moamanager_moa;

chmod 777 -R $dir_opt_moamanager

cd $currentpath;

cp -rv $currentpath/moa/* $dir_opt_moamanager_moa

chmod 777 -R $dir_opt_moamanager_moa

chmod +x $dir_opt_moamanager_moa/bin/moa2014.jar

