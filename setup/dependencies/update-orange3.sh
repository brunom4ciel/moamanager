#!/bin/bash

# by Bruno Maciel

echo "Install Orange3"

currentpath=$(dirname $(readlink -f $0))
dir_opt_moamanager_statistical_orange3=/opt/moamanager/statistical/orange3/

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

sudo mkdir $dir_opt_moamanager_statistical_orange3

sudo chmod 777 -R $dir_opt_moamanager_statistical_orange3

#dir=$(pwd)

mv -f $currentpath/statistical/orange3/* $dir_opt_moamanager_statistical_orange3

mv -f /opt/moamanager/statistical/orange3/scoring.py /opt/moamanager/orange3/Orange/evaluation/scoring.py

python_dist="${python3 -c 'import site; print(site.getsitepackages()[0])'"

sudo pip3 install --target="${python_dist}" bottleneck
sudo pip3 install --target="${python_dist}" pyparsing
sudo pip3 install --target="${python_dist}" pyqt5
sudo pip3 install --target="${python_dist}" sklearn


#sudo -H -u www-data pip3 install bottleneck








