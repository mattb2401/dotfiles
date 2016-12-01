#!/bin/bash 

# Script parameters
#	Param 1: Script run location
#	Param 2: global profile name
#	Param 3: local profile name
#	Param 4-: Any passed argument
#
# When calling oi use the --profile=PROFILE_NAME and 
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.
#
# To post back oi commands print command prefixed by command| to standard output
# To post a comment print to std output prefixed by comment|
# To post an error print to std output prefixed by error|

if [ "$2" = "get-command-definitions" ]; then
	# Definition format usually represented as a single line:

	# Script description|
	# command1|"Command1 description"
	# 	param|"Param description" end
	# end
	# command2|"Command2 description"
	# 	param|"Param description" end
	# end

	echo "Manage wlan connections (default lists available ssids)|"
	echo "connect|\"Connects to a wireless network\" "
	echo "	SSID|\"Network to connect to\" end "
	echo "end "
    echo "scan|\"Scans for new available networks\" end "
	exit
fi

if [ "$4" = "connect" ]; then
    nmcli con list id "$5" &> /dev/null
    if [ $? != 0 ]; then
        nmcli dev wifi connect "$5" &
    else
        nmcli con up id "$5" &
    fi
	exit
fi
if [ "$4" = "scan" ]; then
    if [[ $EUID -eq 0 ]]; then
        iwlist wlan0 scan &> /dev/null
    else
       echo "error|Can only scan for networks as root." 
       exit 1
    fi
fi
ROOT=$(cd $(dirname "$0"); pwd)
$ROOT/wlan-files/parse-wifis.py
#iwlist wlan0 scan|grep ESSID|sed s/ESSID:\"//g|sed s/\"//g|sed 's/^ *//'|grep -v '^$\|^\s*\#'|awk '!a[$0]++'
