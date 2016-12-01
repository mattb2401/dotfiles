#!/usr/bin/env python
import os
import sys
import time
import threading 
import subprocess

commandCompleted = False

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

def printDefinitions():
    # Definition format usually represented as a single line:

    # Script description|
    # command1|"Command1 description"
    #   param|"Param description" end
    # end
    # command2|"Command2 description"
    #   param|"Param description" end
    # end
    write('Initialize OpenIDE configuration point and setting up profile and sublime project|')
    write('[LANGUAGES]|"Enabled languages for this config point using the first language as default language (C#,py)" end ')
    write('[all]|"Initialize with all languages" end ')

def write(message):
    sys.stdout.write(message + '\n')
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
    while True:
        line = sys.stdin.readline()
        if line == "end-of-command\n":
            commandCompleted = True
            break

def runCommand(command):
    commandCompleted = False;
    waitForEndOfCommand = threading.Thread(target = readThread)
    waitForEndOfCommand.start()
    write(command)
    waitForEndOfCommand.join()

def main(argv):
    if len(argv) > 1:
        if argv[2] == 'get-command-definitions':
            printDefinitions()
            return
    arglist = ""
    for arg in argv[4:]:
        arglist = arglist + " \"" + arg + "\""

    directoryName = os.path.basename(argv[1])
    profileName = toSingleLine(['oi', 'conf', 'read', 'init.profile'])
    profilePath = os.path.join(".OpenIDE", directoryName + ".sublime-project")
    if profileName != "":
        profilePath = os.path.join(".OpenIDE", 'profile.' + profileName, directoryName + ".sublime-project")
    
    runCommand("command-builtin|init" + arglist)
    if profileName != "":
        runCommand("command|profile clone " + profileName)
        runCommand("command|profile load " + profileName)
    runCommand("command|conf editor.sublime.project=" + profilePath)
    fullSublimeProjectFile = os.path.join(argv[1], profilePath)
    fileHandle = open(fullSublimeProjectFile, 'w')
    fileHandle.write("{\n")
    fileHandle.write("  \"folders\":\n")
    fileHandle.write("  [\n")
    fileHandle.write("      {\n")
    fileHandle.write("          \"path\": \"" + argv[1] + "\"\n")
    fileHandle.write("      }\n")
    fileHandle.write("  ]\n")
    fileHandle.write("}")
    fileHandle.close()

if __name__ == "__main__":
    main(sys.argv)
