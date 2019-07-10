#!/bin/bash


# Install script for Latest MOAManager by Bruno Maciel
echo "Install wilcoxon-test \n"

currentpath=$(dirname $(readlink -f $0))

dir_opt="/opt"
dir_opt_moamanager="$dir_opt/moamanager"
dir_opt_moamanager_statistical="$dir_opt_moamanager/statistical"

cd $currentpath;

cp -rv $currentpath/statistical/wilcoxon.cpp $dir_opt_moamanager_statistical_wilcoxon/wilcoxon.cpp

chmod 777 -R $dir_opt

cd $dir_opt_moamanager_statistical

g++ $dir_opt_moamanager_statistical/wilcoxon.cpp -std=c++11 -o3 -o $dir_opt_moamanager_statistical/wilcoxon_run

chmod 777 $dir_opt_moamanager_statistical/wilcoxon_run

chmod +x $dir_opt_moamanager_statistical/wilcoxon_run



