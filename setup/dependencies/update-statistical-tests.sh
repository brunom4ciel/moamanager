#!/bin/bash


echo "Update script for Latest MOAManager by Bruno Maciel\n"

echo "Update Statistical Tests\n"

currentpath=$(dirname $(readlink -f $0))

dir_opt="/opt"
dir_opt_moamanager="$dir_opt/moamanager"
dir_opt_moamanager_statistical="$dir_opt_moamanager/statistical"

dir_opt_moamanager_statistical_friedmantest="$dir_opt_moamanager_statistical/friedman-test"
dir_opt_moamanager_statistical_friedmantest_src="$dir_opt_moamanager_statistical_friedmantest/src"
dir_opt_moamanager_statistical_friedmantest_bin="$dir_opt_moamanager_statistical_friedmantest/bin"

cd $currentpath;

cp -rv $currentpath/statistical/friedman/src/* $dir_opt_moamanager_statistical_friedmantest_src

cd $dir_opt_moamanager_statistical_friedmantest_src

g++ $dir_opt_moamanager_statistical_friedmantest_src/friedman_run.cpp $dir_opt_moamanager_statistical_friedmantest_src/Friedman.cpp -std=c++11 -o3 -o $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run

chmod 777 $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run

chmod +x $dir_opt_moamanager_statistical_friedmantest_bin/friedman_run


