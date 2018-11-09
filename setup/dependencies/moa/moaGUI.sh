#!/bin/bash

BASEDIR=$(dirname $(readlink -f $0))
MEMORY=512m

java -Xmx$MEMORY -cp "$BASEDIR/bin/moa2014.jar:$BASEDIR/lib/*" -javaagent:"$BASEDIR/lib/sizeofag-1.0.0.jar" moa.gui.GUI

