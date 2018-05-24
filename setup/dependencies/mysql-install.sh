#!/bin/bash

echo "Install MySQL"

currentpath=$(dirname $(readlink -f $0))

cd $currentpath;

sudo apt-get install mysql-server -y


