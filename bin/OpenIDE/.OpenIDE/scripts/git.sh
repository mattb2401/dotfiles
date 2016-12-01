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

	echo "Various git commands|"
    echo 'renameall|"Rename files, folder and contents" '
    echo '  LOOKFOR|"Value to look for" '
    echo '    REPLACEWITH|"Value replace with" '
    echo '      FOLDERARGUMENTLIST|"List of arguments that are spesific relative folders to replace in" end '
    echo '    end '
    echo '  end '
    echo 'end '
	exit
fi

function replaceinfiles {
    cd $1
    for dir in `find -type d -name "*$2*"` ; do
		git mv "${dir}" "${dir/$2/$3}"
	done 

	for filename in `find -type f -name "*$2*"` ; do
		git mv "${filename}" "${filename/$2/$3}"
	done 
    find -type f -name "*.*"| xargs sed -i -e "s/$2/$3/g"
}

if [[ "$4" == "renameall" ]]; then
    if (( $#  > 6 )); then
        for dir in ${@:7} ; do
            replaceinfiles $1/$dir $5 $6
        done
    else
        replaceinfiles $1 $5 $6
    fi
fi

