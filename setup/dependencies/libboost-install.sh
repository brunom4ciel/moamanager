#!/bin/bash

echo "Install lib boost"

apt-get update -y
apt-get install libboost-all-dev -y

#dpkg -s libboost-dev | grep 'Version'

