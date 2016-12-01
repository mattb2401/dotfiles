#!/usr/bin/env python
import sys
import os

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

def write(message):
    sys.stdout.write(message + "\n")

def printDefinitions():
    # Definition format usually represented as a single line:

    # Script description|
    # command1|"Command1 description"
    # 	param|"Param description" end
    # end
    # command2|"Command2 description"
    # 	param|"Param description" end
    # end
    write("Opens file in editor|")
    write('FILE|"Absolute or relative path to file. Will also try to parse line and column." end ')

def main(argv):
    if len(argv) > 1:
        if argv[2] == 'get-command-definitions':
            printDefinitions()
            return
    if len(sys.argv) >= 5:
        current = os.getcwd()
        chunks = sys.argv[4].split(':')
        line='0'
        column='0'
        if len(chunks) > 1 and chunks[1] != '':
            line=chunks[1]
        if len(chunks) > 2 and chunks[2] != '':
            c = chunks[2]
            if len(c) < 10 and ' ' not in c and '/' not in c and '\\' not in c:
                column=c

        if os.path.isfile(chunks[0]):
            fullpath = os.path.realpath(chunks[0])
        else:
            os.chdir(argv[1])
            fullpath = os.path.realpath(chunks[0])

        if os.path.isfile(fullpath):
        	write("command|editor goto \"" + fullpath + "|" + line + "|" + column + "\"")
        os.chdir(current)


if __name__ == "__main__":
    main(sys.argv)
