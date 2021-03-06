#!/bin/bash

# by Bruno Maciel

echo "Install Orange3"

currentpath=$(dirname $(readlink -f $0))
dir_opt_moamanager_statistical_orange3=/opt/moamanager/statistical/orange3/

mkdir $dir_opt_moamanager_statistical_orange3

chmod 777 -R $dir_opt_moamanager_statistical_orange3

apt-get install python-pip -y
apt-get install python3-dev python3-numpy python3-scipy linuxbrew-wrapper python3-pip -y

pip3 install pyqt5

cd /opt/moamanager/

git clone https://github.com/biolab/orange3

cd /opt/moamanager/orange3/

pip3 install -r requirements-gui.txt
pip3 install -r requirements.txt

python3 setup.py develop

chmod 777 -R /opt/moamanager/orange3
 
cp -rv $currentpath/statistical/orange3/* $dir_opt_moamanager_statistical_orange3

cp -rv /opt/moamanager/statistical/orange3/scoring.py /opt/moamanager/orange3/Orange/evaluation/scoring.py
 
#dir=$(pwd)
#mv -f /opt/moamanager/statistical/orange3/scoring.py /opt/moamanager/orange3/Orange/evaluation/scoring.py

python_dist="${python3 -c 'import site; print(site.getsitepackages()[0])'"

pip3 install --target="${python_dist}" bottleneck
pip3 install --target="${python_dist}" pyparsing
pip3 install --target="${python_dist}" pyqt5
pip3 install --target="${python_dist}" sklearn

#sudo -H -u www-data pip3 install bottleneck




