#!/bin/bash

# by Bruno Maciel

echo "Update Orange3"

currentpath=$(dirname $(readlink -f $0))
dir_opt_moamanager_statistical_orange3=/opt/moamanager/statistical/orange3/

mkdir $dir_opt_moamanager_statistical_orange3

chmod 777 -R $dir_opt_moamanager_statistical_orange3

cd /opt/moamanager/

#mv /opt/moamanager/orange3/ /opt/moamanager/orange3aux/

git clone https://github.com/biolab/orange3

cd /opt/moamanager/orange3/

pip3 install -r requirements-gui.txt
pip3 install -r requirements.txt

python3 setup.py develop

cp -rv $currentpath/statistical/orange3/* $dir_opt_moamanager_statistical_orange3

cp -rv /opt/moamanager/statistical/orange3/scoring.py /opt/moamanager/orange3/Orange/evaluation/scoring.py





