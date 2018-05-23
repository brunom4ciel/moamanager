#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel

echo "Install script for Latest MOA by Bruno Maciel\n"

echo "Install friedman-test \n"

dirbase1=$(dirname $(readlink -f $0))

dirinstall="/opt"
dirbase="$dirinstall/moamanager"
dirbase_statistical="$dirbase/statistical"

dirbase_statistical_friedman="$dirbase/friedman-test"
dirbase_statistical_friedman_src="$dirbase_statistical_friedman/src"
dirbase_statistical_friedman_bin="$dirbase_statistical_friedman/bin"

echo "create folder in path $dirbase"
mkdir -p $dirbase;

echo "create folder in path $dirbase_statistical"
mkdir -p $dirbase_statistical;

echo "create folder in path $dirbase_statistical_friedman"
mkdir -p $dirbase_statistical_friedman;

chmod 777 -R $dirbase

cd $dirbase_statistical_friedman_src;

mv $dirbase1/dependencies/friedman/src/* $dirbase_statistical_friedman_src

chmod 0777 -R $dirbase

cd $dirbase1/dependencies/friedman/src/

g++ $dirbase_statistical_friedman_src/friedman_run.cpp $dirbase_statistical_friedman_src/Friedman.cpp -std=c++11 -o3 -o $dirbase_statistical_friedman_bin/friedman_run

chmod 777 $dirbase_statistical_friedman_bin/friedman_run

chmod +x $dirbase_statistical_friedman_bin/friedman_run



