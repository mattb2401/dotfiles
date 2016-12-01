#!/bin/bash

ROOT=$(cd $(dirname "$0"); pwd)
WORKING_DIR="$1"
DEFAULT_LANGUAGE="$2"
ENABLED_LANGUAGES="$3"

if [ "$DEFAULT_LANGUAGE" == "C#" ]; then
    cd "$WORKING_DIR"
    ISALIVE=$(ps -Af|grep -e "ContinuousTests.exe --bellyrub --leave-in-background ${WORKING_DIR}$"|wc -l)
    if [ "$ISALIVE" == "0" ]; then
        CONFIG_DIR="$(oi conf read rootpoint)"
        SLN="$(oi conf read autotest.solution)"
        cd "$ROOT"

        WATCH_PATH="$WORKING_DIR"
        if [[ "$SLN" != "" ]]; then
            WATCH_PATH=$CONFIG_DIR/$SLN
        fi
        oi process start "/home/ack/bin/ContinuousTests/ContinuousTests.exe" --bellyrub --leave-in-background "$WATCH_PATH" &
    fi
fi

if [ "$DEFAULT_LANGUAGE" == "f#" ]; then
    cd "$WORKING_DIR"
    ISALIVE=$(ps -Af|grep -e "ContinuousTests.exe --bellyrub --leave-in-background ${WORKING_DIR}$"|wc -l)
    if [ "$ISALIVE" == "0" ]; then
        CONFIG_DIR="$(oi conf read rootpoint)"
        SLN="$(oi conf read autotest.solution)" 
        cd "$ROOT"

        WATCH_PATH="$WORKING_DIR"
        if [[ "$SLN" != "" ]]; then
            WATCH_PATH=$CONFIG_DIR/$SLN
        fi
        
        oi process start "/home/ack/bin/ContinuousTests/ContinuousTests.exe" --bellyrub --leave-in-background "$WATCH_PATH" &
    fi
fi
