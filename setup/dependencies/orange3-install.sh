#!/bin/bash

# by Bruno Maciel

echo "Install Orange3"

sudo apt-get install python-pip -y
sudo apt-get install python3-dev python3-numpy python3-scipy linuxbrew-wrapper python3-pip -y

pip3 install pyqt5

cd /opt/moamanager/

git clone https://github.com/biolab/orange3

cd /opt/moamanager/orange3/

pip3 install -r requirements-gui.txt
pip3 install -r requirements.txt

python3 setup.py develop

sudo chmod 777 -R /opt/moamanager/orange3
 
#dir=$(pwd)

mv -f /opt/moamanager/statistical/orange3/scoring.py /opt/moamanager/orange3/Orange/evaluation/scoring.py








