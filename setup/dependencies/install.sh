#!/bin/bash


# by Bruno Maciel

dirbase=$(dirname $(readlink -f $0))
dirinstall="$dirmoam/dependencies"

cd $dirinstall

sh apt-get-update.sh

sh apache2-install.sh
sh curl-install.sh
sh mysql-install.sh
sh php-install.sh

service apache2 restart

sh java-install.sh
sh moa-install.sh
sh gcc-install.sh
sh libboost-install.sh
sh friedman-install.sh
sh wilcoxon-install.sh

sh moamanager-install.sh

sh orange3-install.sh

sh checking-installed.sh
