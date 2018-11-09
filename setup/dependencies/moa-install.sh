#!/bin/bash

echo "Install script for Latest MOA by Bruno Maciel\n"

echo "Install MOA\n"

currentpath=$(dirname $(readlink -f $0))

dir_opt="/opt"
dir_opt_moamanager="$dir_opt/moamanager"
dir_opt_moamanager_moa="$dir_opt_moamanager/moa"

echo "create folder in path $dir_opt_moamanager"
mkdir -p $dir_opt_moamanager;

echo "create folder in path $dir_opt_moamanager_moa"
mkdir -p $dir_opt_moamanager_moa;
 
chmod 777 -R $dir_opt_moamanager

cd $currentpath/moa;

cp -rv $currentpath/moa/* $dir_opt_moamanager_moa

chmod 777 -R $dir_opt_moamanager_moa

#cd $dir_opt_moamanager_moa/bin
#sudo jar vcf moa2014.jar $dir_opt_moamanager_moa/bin/*
#cp -rv $dir_opt_moamanager_moa/moa2014.jar $dir_opt_moamanager_moa/bin/moa2014.jar
#chmod +x $dir_opt_moamanager_moa/bin/moa2014.jar



#dirbase=$dir_opt_moamanager_moa

# program defaults
#LIBPATH="$dirbase/lib"

#class_path=$LIBPATH/weka-3-7-12-monolithic.jar;
# get source files
#find lib/ -name "*.jar" 1> classesjar

# get source files
find src/ -name "*.java" > sources

#echo Compiling $class_path 

classp="lib/sizeofag-1.0.0.jar:lib/commons-math-2.1.jar:lib/Jama.jar:lib/weka-3-7-12-monolithic.jar:lib/commons-math3-3.6.1.jar:lib/guava-18.0.jar"

rm -fr "$dir_opt_moamanager_moa/bin2";
mkdir -p "$dir_opt_moamanager_moa/bin2";
chmod 777 -R "$dir_opt_moamanager_moa/bin2"

javac -classpath "$classp" @sources -d bin2 -Xlint:-deprecation -Xlint:unchecked -O -nowarn

rm -fr sources
rm -fr "$dir_opt_moamanager_moa/bin/moa2014.jar"

cd "$dir_opt_moamanager_moa/bin2"

jar cfm "$dir_opt_moamanager_moa/moa2014.jar" "$dir_opt_moamanager_moa/MANIFEST.MF" *

chmod +x "$dir_opt_moamanager_moa/moa2014.jar"
chmod +x "$dir_opt_moamanager_moa/compile_run.sh"

mv "$dir_opt_moamanager_moa/moa2014.jar" "$dir_opt_moamanager_moa/bin/moa2014.jar"

rm -fr "$dir_opt_moamanager_moa/bin2";
