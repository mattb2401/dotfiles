#!/usr/bin/env python
import os, sys, subprocess

def write(message):
    sys.stdout.write(message+"\n")
    sys.stdout.flush()

def run_process(exe,workingDir=""):    
    if workingDir == "":
        workingDir = os.getcwd()
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=workingDir)
    lines = []
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode(encoding='windows-1252').strip('\n').strip('\r')
        if line != "":
            lines.append(line)
        if(retcode is not None):
            break
    return lines

def print_react_patterns():
    # Write one event pr line that this script will react to
    write("* 'command' 'find-interactive'")
    write("* 'command' 'find-interactive-all'")
    write("* 'command' 'find-interactive-yml'")
    write("* 'command' 'find-interactive-word'")
    write("'user-inputted' 'find-interactive-input' '*");
    write("'user-inputted' 'find-interactive-input-all' '*");
    write("'user-inputted' 'find-interactive-input-yml' '*");
    write("'user-selected' 'find-interactive-select' '*")

def get_caret():
    output = [];
    sys.stdout.write("request|editor get-caret\n")
    sys.stdout.flush()
    while True:
        line = sys.stdin.readline().strip("\n")
        if line == "end-of-conversation":
            break;
        output.append(line)
    caret = output[0].split("|")
    return caret[0], int(caret[1]), int(caret[2]), output[1:]

def getWordStart(line, column, operators):
    startAt   = column - 1
    # If we are at the start of the word jump one back
    if line[startAt] in operators:
        startAt = startAt - 1
    for i in range(startAt, 1, -1):
        if line[i - 1] in operators:
            return i
    return -1

def getWordEnd(line, column, operators):
    for i in range(column, len(line) + 1):
        if line[i-1] in operators:
            return i-1
    return len(line)

def getWord(line, column, lines):
    operators = ['{', '}', '[', ']', '(', ')', '.', ',', "'", '"', '+', '-', '/', '\\', '>', '<', '*', '^', '=', '!', '&', ':', ';', ' ', "\n", '@']
    line = lines[line - 1]
    start = getWordStart(line, column, operators)
    if start == -1:
        return ""
    end = getWordEnd(line, column, operators)
    return line[start:end]

def performFind(prefix, command, event):
    start = len(prefix)
    searchText = event[start:(len(event) - 1)]
    lines = run_process(['oi',command,searchText])
    results = ""
    separator = ""
    for line in lines:
        trimmedLine = line.strip("\n").strip("\r").replace(",", "")
        columns = trimmedLine.split(':')
        results = results+separator+columns[0]+'|'+columns[1]+'|0||'+trimmedLine
        separator = ","
    write('command|editor user-select "find-interactive-select" "'+results+'"')

def handle_event(event, global_profile, local_profile, args):
    # Write scirpt code here.
    inputPrefix = "'user-inputted' 'find-interactive-input' '"
    inputPrefixAll = "'user-inputted' 'find-interactive-input-all' '"
    inputPrefixYml = "'user-inputted' 'find-interactive-input-yml' '"
    selectPrefix = "'user-selected' 'find-interactive-select' '"
    if event.endswith(" 'command' 'find-interactive'"):
        write('command|editor user-input "find-interactive-input"')
    elif event.endswith(" 'command' 'find-interactive-all'"):
        write('command|editor user-input "find-interactive-input-all"')
    elif event.endswith(" 'command' 'find-interactive-yml'"):
        write('command|editor user-input "find-interactive-input-yml"')
    elif event.endswith(" 'command' 'find-interactive-word'"):
        filename, line, column, lines = get_caret()
        event = "'" + getWord(line, column, lines) + "'"
        if event != "''":
            performFind("'", 'find', event)
    elif event.startswith(inputPrefix):
        performFind(inputPrefix, 'find', event)
    elif event.startswith(inputPrefixAll):
        performFind(inputPrefixAll, 'find-all', event)
    elif event.startswith(inputPrefixYml):
        performFind(inputPrefixYml, 'find-yml', event)
    elif event.startswith(selectPrefix):
        start = len(selectPrefix)
        match = event[start:(len(event) - 1)]
        if "|" in match:
            file, line, column = match.split('|')
            write('command|editor goto "'+file+"|"+line+"|"+column+'"')

if __name__ == "__main__":
    #   Param 1: event
    #   Param 2: global profile name
    #   Param 3: local profile name
    #
    # When calling other commands use the --profile=PROFILE_NAME and 
    # --global-profile=PROFILE_NAME argument to ensure calling scripts
    # with the right profile.
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        print_react_patterns()
    else:
        handle_event(args[1], args[2], args[3], args[4:])
