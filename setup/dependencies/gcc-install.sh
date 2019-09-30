#!/bin/bash

echo "Install GCC"

apt-get install build-essential software-properties-common -y 
add-apt-repository ppa:ubuntu-toolchain-r/test -y 

apt-get update -y

apt-get install gcc-7 g++-7 -y 
update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-7 60 --slave /usr/bin/g++ g++ /usr/bin/g++-7 
update-alternatives --config gcc


