#!/bin/bash


echo "Update script for Latest MOAManager by Bruno Maciel\n"

echo "Update MOAManager\n"

sh update-moamanager.sh

echo "Update MOA\n"

sh update-moa.sh

echo "Update Statistical Tests\n"

sh update-statistical-tests.sh

#echo "Update Orange3\n"

#sh update-orange3.sh
