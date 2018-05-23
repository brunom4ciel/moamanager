#!/bin/bash

# by Bruno Maciel

dirbase=$(dirname $(readlink -f $0))
dirinstall="$dirbase/dependencies"

cd $dirinstall

sh install.sh


