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

	echo "Brings the terminal from the current desktop to front|"
	echo "[[process]]|\"\" "
	echo "	[[set-to-foreground]]|\"\" "
	echo "		focus-terminal|\"Bring terminal in current workspace to front\" end "
	echo "	end "
	echo "end "
	exit
fi

wmctrl -xlG | grep gnome-terminal | awk '$3>=0 && $3<1600 && $4>=0 && $4<900 {print $1}' | while read WIN; do wmctrl -ia "$WIN"; done