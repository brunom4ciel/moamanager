#!/bin/bash

# by Bruno Maciel

currentpath=$(dirname $(readlink -f $0))

dirinstall="$currentpath/dependencies"

cd $dirinstall

sh update.sh


