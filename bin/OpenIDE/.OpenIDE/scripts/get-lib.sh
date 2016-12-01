#!/bin/bash 

if [ "$2" = "get-command-definitions" ]; then
	echo "Copys library to lib folder|"
	echo "nancy|\"Copys Nancy libraries to lib folder\" end "
	echo "nunit|\"Copys NUnit libraries to lib folder\" end "
	echo "jsonnet|\"Copys Json.Net libraries to lib folder\" end "
	exit
fi

LIB=$(pwd)/lib
if [[ ! -d "$LIB" ]]; then
	mkdir "$LIB"
fi

if [ "$4" = "nancy" ]; then
	SOURCE=~/src/libs/Nancy
	DEST=$LIB/Nancy
	if [[ ! -d "$DEST" ]]; then
		mkdir $DEST
		cp -r $SOURCE/* $DEST
	fi
fi
if [ "$4" = "nunit" ]; then
	SOURCE=~/src/libs/NUnit
	DEST=$LIB/NUnit
	if [[ ! -d "$DEST" ]]; then
		mkdir $DEST
		cp -r $SOURCE/* $DEST
	fi
fi
if [ "$4" = "jsonnet" ]; then
	SOURCE=~/src/libs/JSON.NET/Net40
	DEST=$LIB/JSON.NET
	if [[ ! -d "$DEST" ]]; then
		mkdir $DEST
		cp -r $SOURCE/* $DEST
	fi
fi