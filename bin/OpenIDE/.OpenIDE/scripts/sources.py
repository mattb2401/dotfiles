#!/usr/bin/env python
import os
import sys
import glob
import subprocess
import threading

# Script parameters
#   Param 1: Script run location
#   Param 2: global profile name
#   Param 3: local profile name
#   Param 4-: Any passed argument
#
# When calling oi use the --profile=PROFILE_NAME and 
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.
#
# To post back oi commands print command prefixed by command| to standard output
# To post a comment print to std output prefixed by comment|
# To post an error print to std output prefixed by error|

def write(text):
    sys.stdout.write(text + '\n')
    sys.stdout.flush()

def runProcess(exe,workingDir=""):    
    if workingDir == "":
        workingDir = os.getcwd()
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=workingDir)
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode(encoding='windows-1252').strip('\n').strip('\r')
        if line != "":
            yield line
        if(retcode is not None):
            break

def toSingleLine(exe, workingDir=""):
    lines = ""
    for line in runProcess(exe, workingDir):
        lines = lines + line
    return lines

def readThread():
    line = sys.stdin.readline()
    while line:
        if line == "end-of-command\n":
            break

def runCommand(command):
    waitForEndOfCommand = threading.Thread(target = readThread)
    waitForEndOfCommand.start()
    write(command)
    waitForEndOfCommand.join()

def printDefinitions():
    # Definition format usually represented as a single line:

    # Script description|
    # command1|"Command1 description"
    #   param|"Param description" end
    # end
    # command2|"Command2 description"
    #   param|"Param description" end
    # end
    write('Package source management|')
    write('create|"Creates source list for default package location"')
    write(' NAME|"Name of the package source to create" ')
    write('   [--default]|"Will use the default configured package directory" end ')
    write('  end ')
    write('end ')
    write('update|"Updates source list for default package location"')
    write(' NAME|"Name of the package source to update" ')
    write('   [--default]|"Will use the default configured package directory" end ')
    write('  end ')
    write('end ')
    write('[[package]]|\"\" ')
    write(' build|\"Builds package and updates sources in default package destination\" ')
    write('  SOURCE|\"Ex. .OpenIDE/scripts/myscript\" ')
    write('   [DESTINATION]|\"Destination directory (default destination from config)\" end ')
    write('  end ')
    write(' end ')
    write('end\n')

def main(argv):
    if len(argv) > 1:
        if argv[2] == 'get-command-definitions':
            printDefinitions()
            return
    if len(sys.argv) < 5:
        return
    command = sys.argv[4]
    if (command == 'build'):
        arglist = ""
        for arg in argv[5:]:
            arglist = arglist + " \"" + arg + "\""
        runCommand('command-original|package build'+arglist)
        packageDir = toSingleLine(['oi', 'conf', 'read', 'default.package.destination'])
        if os.path.isdir(packageDir) == False:
            return
        # Only update sources if passed package dir is default package dir
        if len(argv) > 6 and argv[6] != packageDir:
            return
        for file in os.listdir(packageDir):
            if file.endswith(".source"):
                name = os.path.splitext(file)[0]
                for line in runProcess(['oi', 'sources', 'update', name, "--default"], packageDir):
                    write(line)
        return
    
    if (command == 'create' or command == 'update') and len(sys.argv) > 5:
        name = sys.argv[5]
        packagedestination = argv[1]
        if len(argv) > 6 and argv[6] == '--default':
            packagedestination = toSingleLine(["oi", "conf", "read", "default.package.destination"])
        if packagedestination == "":
            write("The default.package.destination setting is not in the configuration")
            return
        oiroot = toSingleLine(["oi", "conf", "read", "rootpoint", "-g"])
        packagefile = os.path.join(packagedestination, name + ".source")
        pckmngr = os.path.join(oiroot, "Packaging", "oipckmngr.exe")
        arguments = [pckmngr, "source", command, packagefile]
        if command == 'create':
            arguments.append(packagefile)
        write(command + ' source '+name+'...')
        for line in runProcess(arguments):
            write(line + "\n")

if __name__ == "__main__":
    main(sys.argv)