#!/bin/bash


# by Bruno Maciel

echo "Install JAVA"

sudo apt-get install ppa-purge -y
sudo apt-get install python-software-properties -y
sudo add-apt-repository ppa:webupd8team/java -y
sudo apt-get update -y
sudo apt-get install oracle-java8-installer -y
sudo apt-get install oracle-java8-set-default -y









