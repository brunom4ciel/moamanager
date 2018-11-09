#!/bin/bash

echo "Install lib boost"

sudo apt-get update -y
sudo apt-get install libboost-all-dev -y

#dpkg -s libboost-dev | grep 'Version'

