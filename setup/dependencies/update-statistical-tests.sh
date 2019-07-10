#!/bin/bash


echo "Update script for Latest MOAManager by Bruno Maciel\n"

echo "Update Statistical Tests\n"

echo "Update gcc\n"
sh gcc-install.sh

echo "Update libboost\n"
sh libboost-install.sh

echo "Update friedman-test and post-hoc tests\n"
sh friedman-install.sh

echo "Update Wilcoxon signed-rank test\n"
sh wilcoxon-install.sh

echo "Update Orange3\n"
sh orange3-install.sh


