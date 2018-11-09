#!bin/bash

dir_opt_moamanager_moa=$(dirname $(readlink -f $0))

find src/ -name "*.java" > sources

#echo Compiling $class_path 

classp="lib/sizeofag-1.0.0.jar:lib/commons-math-2.1.jar:lib/Jama.jar:lib/jdistlib-0.4.1-bin.jar:lib/weka-3-7-12-monolithic.jar:lib/commons-math3-3.6.1.jar:lib/guava-18.0.jar"

rm -fr "$dir_opt_moamanager_moa/bin2";
mkdir -p "$dir_opt_moamanager_moa/bin2";
chmod 777 -R "$dir_opt_moamanager_moa/bin2"

javac -classpath "$classp" @sources -d bin2 -Xlint:deprecation -Xlint:unchecked -O -nowarn

rm -fr sources
rm -fr "$dir_opt_moamanager_moa/bin/moa2014.jar"

#echo "jar cfm moa2014.jar MANIFEST.MF $dir_opt_moamanager_moa/bin/*.class"
#currentpathmoa="$currentpath/moa"


cd "$dir_opt_moamanager_moa/bin2"

jar cfm "$dir_opt_moamanager_moa/moa2014.jar" "$dir_opt_moamanager_moa/MANIFEST.MF" *


chmod +x "$dir_opt_moamanager_moa/moa2014.jar"

mv "$dir_opt_moamanager_moa/moa2014.jar" "$dir_opt_moamanager_moa/bin/moa2014.jar"


#rm -fr "$dir_opt_moamanager_moa/bin/moa"
#rm -fr "$dir_opt_moamanager_moa/bin/weka"

rm -fr "$dir_opt_moamanager_moa/bin2";
