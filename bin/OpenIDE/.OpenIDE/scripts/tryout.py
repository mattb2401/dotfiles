#!/usr/bin/env python
import os
import sys
import time
import random
import tempfile
import subprocess
import shutil


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
    print(text)
    sys.stdout.flush()

def printDefinitions():
    # Definition format usually represented as a single line:

    # Script description|
    # command1|"Command1 ttescription"
    #   param|"Param description" end
    # end
    # command2|"Command2 description"
    #   param|"Param description" end
    # end
    print("Create a temporary space for trying out stuff|")
    print("python|\"Tryout stuff in python\" end ")
    print("php|\"Tryout stuff in php\" end ")
    print("C#|\"Tryout stuff in C# (Requires ContinuousTests or AutoTest.Net)\" end ")

def runProcess(exe,workingDir = ''):
    if workingDir == '':
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

def setupPythonFiles(dir):
    scriptDir = os.path.dirname(__file__)
    name = os.path.splitext(os.path.basename(__file__))[0]
    filesdir = os.path.join(scriptDir,name + "-files")
    reactiveScript = os.path.join(filesdir, "python","runpython.py")
    destination = os.path.join(dir,".OpenIDE","rscripts")
    os.makedirs(destination)
    shutil.copyfile(reactiveScript,os.path.join(destination, "runpython.py"))
    shutil.copyfile(os.path.join(filesdir, "python","main.py"), os.path.join(dir, "main.py"))
    shutil.copymode(reactiveScript,os.path.join(destination, "runpython.py"))
    shutil.copymode(os.path.join(filesdir, "python","main.py"), os.path.join(dir, "main.py"))

def setupPhpFiles(dir):
    scriptDir = os.path.dirname(__file__)
    name = os.path.splitext(os.path.basename(__file__))[0]
    filesdir = os.path.join(scriptDir,name + "-files")
    reactiveScript = os.path.join(filesdir, "php", "runphp.py")
    destination = os.path.join(dir,".OpenIDE","rscripts")
    os.makedirs(destination)
    shutil.copyfile(reactiveScript,os.path.join(destination, "runphp.py"))
    shutil.copyfile(os.path.join(filesdir, "php", "main.php"), os.path.join(dir, "main.php"))
    shutil.copymode(reactiveScript,os.path.join(destination, "runphp.py"))
    shutil.copymode(os.path.join(filesdir, "php", "main.php"), os.path.join(dir, "main.php"))

def setupCSFiles(dir):
    scriptDir = os.path.dirname(__file__)
    name = os.path.splitext(os.path.basename(__file__))[0]
    filesdir = os.path.join(scriptDir,name + "-files")
    reactiveScript = os.path.join(filesdir, "C#","runcs.py")
    destination = os.path.join(dir,".OpenIDE","rscripts")
    os.makedirs(destination)
    shutil.copyfile(reactiveScript,os.path.join(destination, "runcs.py"))
    shutil.copymode(reactiveScript,os.path.join(destination, "runcs.py"))
    for item in runProcess(["oi", "create","console","tryout.csproj"], dir):
        pass

def setupConfig(dir, language):
    for item in runProcess(["oi","init",language], dir):
        meh = ""
    if language == "python":
        setupPythonFiles(dir)
    if language == "php":
        setupPhpFiles(dir)
    if language == "C#":
        setupCSFiles(dir)

def getReactiveListener(language):
    if language == "python":
        return "rscript-runpython*"
    if language == "php":
        return "rscript-runphp*"
    if language == "C#":
        return "rscript-runcs*"

def getMainFileName(language):
    if language == "python":
        return "main.py"
    if language == "php":
        return "main.php"
    if language == "C#":
        return "Program.cs"
    return None

def initProject():
    if len(sys.argv) <= 4:
        return
    language = sys.argv[4]
    dir = tempfile.mkdtemp(prefix=language + "-")
    write(dir)
    setupConfig(dir, language)
    editor = toSingleLine(['oi', 'conf', 'read', 'tryout.editor'])
    if editor != "":
        subprocess.Popen(["oi","editor",editor], cwd=dir)
    else:
        subprocess.Popen(["oi","editor"], cwd=dir)
    time.sleep(2)
    mainFileName = getMainFileName(language)
    if mainFileName == None:
        return
    mainFile = os.path.join(dir, mainFileName)
    subprocess.Popen(["oi","editor","goto", mainFile + "|4|2"], cwd=dir)
    reactive_listener = getReactiveListener(language)
    reactive_listener_len = len(reactive_listener)
    for item in runProcess(["oi","output-listener",reactive_listener], dir):
        write(item)
    shutil.rmtree(dir)

def main(argv):
    if len(argv) > 1:
        if argv[2] == 'get-command-definitions':
            printDefinitions()
            return
    initProject()

if __name__ == "__main__":
    main(sys.argv)
