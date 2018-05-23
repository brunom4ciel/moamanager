#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel

echo "Install script for Latest MOA by Bruno Maciel\n"

echo "Install MOA\n"

dirbase1=$(dirname $(readlink -f $0))
dirinstall="/opt"
dirbase="$dirinstall/moamanager"
dirbase_moa="$dirbase/moa"
dirbase_moa_bin="$dirbase_moa/bin/"
#dirbase_moa_lib="$dirbase_moa/lib/"
#dirbase_moa_datasets="$dirbase_moa/datasets/"

echo "create folder in path $dirbase"
mkdir -p $dirbase;

echo "create folder in path $dirbase_moa"
mkdir -p $dirbase_moa;

#echo "create folder in path $dirbase_moa_bin"
#mkdir -p $dirbase_moa_bin;

#echo "create folder in path $dirbase_moa_lib"
#mkdir -p $dirbase_moa_lib;
 
#echo "create folder in path $dirbase_moa_datasets"
#mkdir -p $dirbase_moa_datasets;

chmod 0777 -R $dirbase

cd $dirbase_moa;

#echo "Download latest moa and uncompress"

#wget https://raw.githubusercontent.com/brunom4ciel/moamanager/master/moa-install.tar.gz

#tar zxf moa-install.tar.gz

#rm moa-install.tar.gz

echo "create folder in path $dirbase1/moa"
mkdir -p $dirbase1/moa;
chmod 0777 -R $dirbase1/moa

tar zxf $dirbase1/moa.tar.gz -C $dirbase1/moa

rm -rf $dirbase_moa/*

mv $dirbase1/moa/* $dirbase_moa


chmod 0777 -R $dirbase

chmod +x $dirbase_moa_bin/moa2014.jar


rm -rf $dirbase1/moa



