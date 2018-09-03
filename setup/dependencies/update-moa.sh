#!/bin/bash


echo "Update script for Latest MOAManager by Bruno Maciel\n"

echo "Update MOA\n"

currentpath=$(dirname $(readlink -f $0))

dirmoa_files=$currentpath/moa

dirmoa_opt="/opt/moamanager/moa"

cd $dirmoa_files;

chmod 777 -R $dirmoa_opt

cp -rv $dirmoa_files/src/* $dirmoa_opt/src

cp -rv $dirmoa_files/lib/* $dirmoa_opt/lib

cp -rv $dirmoa_files/datasets/* $dirmoa_opt/datasets

cp -rv $dirmoa_files/MANIFEST.MF $dirmoa_opt/
cp -rv $dirmoa_files/moaGUI.sh $dirmoa_opt/


chmod 777 -R $dirmoa_opt

