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

	echo "Searches the file contents|"
    echo "SEARCH-STRING|\"Pattern to search for. Defaults to cs file type\" end "
    echo "[--type=]|\"File type to search through\" end "
    echo "[--path=]|\"Will search throug files where full path contains this value\" end "
    echo "[--match-case=]|\"Matches string on exact case\" end "
	exit
fi

if (( $# < 4 )); then
    echo "Illegal number of parameters"
    exit
fi

cd $1
cd $(oi conf read rootpoint)
language=$(oi conf read default.language)

value=""
pathPattern=$(oi conf read find.path)
filetype="*.*"
matchCase="-i"

if [[ "$language" == "php" ]]; then
    filetype="*.php"
elif [[ "$language" == "C#" ]]; then
    filetype="*.cs"
elif [[ "$language" == "js" ]]; then
    filetype="*.js"
elif [[ "$language" == "python" ]]; then
    filetype="*.py"
elif [[ "$language" == "go" ]]; then
    filetype="*.go"
fi

for i in ${@:4}
do
    if [[ $i == --type=* ]]; then
        filetype=*.${i:7}
    elif [[ $i == --path=* ]]; then
        pathPattern=${i:7}
    elif [[ $i == --match-case ]]; then
        matchCase=""
    else
        value=$i
    fi
done

if [ "$pathPattern" == "" ]; then
    find -iname "$filetype"|xargs grep -n -s $matchCase -- "$value"
else
    find -iname "$filetype"|grep $pathPattern|xargs grep -n -s $matchCase -- "$value"
fi
