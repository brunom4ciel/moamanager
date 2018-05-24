#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel
echo "Install friedman-test \n"

currentpath=$(dirname $(readlink -f $0))

dir_opt="/opt"
dir_opt_moamanager="$dir_opt/moamanager"
dir_opt_moamanager_statistical="$dir_opt_moamanager/statistical"

dir_opt_moamanager_statistical_friedmantest="$dir_opt_moamanager_statistical/friedman-test"
dir_opt_moamanager_statistical_friedmantest_src="$dir_opt_moamanager_statistical_friedmantest/src"
dir_opt_moamanager_statistical_friedmantest_bin="$dir_opt_moamanager_statistical_friedmantest/bin"

echo "create folder in path $dir_opt_moamanager"
mkdir -p $dir_opt_moamanager;

echo "create folder in path $dir_opt_moamanager_statistical"
mkdir -p $dir_opt_moamanager_statistical;

echo "create folder in path $dir_opt_moamanager_statistical_friedmantest"
mkdir -p $dir_opt_moamanager_statistical_friedmantest;


echo "create folder in path $dir_opt_moamanager_statistical_friedmantest_src"
mkdir -p $dir_opt_moamanager_statistical_friedmantest_src;

echo "create folder in path $dir_opt_moamanager_statistical_friedmantest_bin"
mkdir -p $dir_opt_moamanager_statistical_friedmantest_bin;

cd $currentpath;

cp -rv $currentpath/statistical/friedman/src/* $dir_opt_moamanager_statistical_friedmantest_src

chmod 777 -R $dir_opt

cd $dir_opt_moamanager_statistical_friedmantest_src

g++ $dir_opt_moamanager_statistical_friedmantest_src/friedman_run.cpp $dir_opt_moamanager_statistical_friedmantest_src/Friedman.cpp -std=c++11 -o3 -o $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run

chmod 777 $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run

chmod +x $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run



