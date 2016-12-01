#!/usr/bin/env python
import os, sys, subprocess

def write(message):
    sys.stdout.write(message+"\n")
    sys.stdout.flush()

def write_react_patterns():
    write("* 'command' 'tamper-at-caret'")
    write("* 'command' 'navigate-at-caret'")
    write("* 'command' 'command-at-caret'")
    write("'user-selected' '*-tamper-at-caret' *")
    write("'user-selected' '*-navigate-at-caret' *")
    write("'user-selected' '*-command-at-caret' *")
    write("run-as-service")
     
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

def get_option(option):
    chunks = option.split("=")
    if len(chunks) != 2:
        return None, None
    name_chunks = chunks[0].split(".")
    offset = len(name_chunks[0])+len(name_chunks[1])+5
    name = chunks[0][offset:].replace(".", " ").title().strip()
    response_event = chunks[1]
    return name, response_event, name_chunks[0]

def get_name(option):
    name, response_event, language = get_option(option)
    return name

def get_event(option):
    name, response_event, language = get_option(option)
    return response_event

def get_language(option):
    name, response_event, language = get_option(option)
    return language

def option_starts_with(languages, options, base, filetype):
    matches = []
    for language in languages:
        match = base+"."+language+filetype+"."
        match2 = base+"."+language+".any."
        for option in options:
            if option.startswith(match):
                matches.append(option)
            elif option.startswith(match2):
                matches.append(option)
    return matches

def option_is(languages, options, base, filetype, choice):
    matches = []
    for language in languages:
        match = base+"."+language+filetype+"."+choice
        match2 = base+"."+language+".any."+choice
        for option in options:
            if option.startswith(match+"="):
                matches.append(option)
            elif option.startswith(match2+"="):
                matches.append(option)
    return matches

def handle_command(languages, actions, event, scope):
    chunks = event.split(" ")
    filetype = chunks[0][1:len(chunks[0])-1]
    base = "contextual."+scope
    options = option_starts_with(languages, actions, base, filetype)
    option_string = (''.join(map(lambda x: get_name(x[len(base):])+',', options))).strip(",")
    if len(option_string) > 0:
        write("command|editor user-select \"" + filetype + "-"+scope+"-at-caret\" \"" + option_string + "\"")

def handle_select(languages, actions, event, scope):
    identifier = "-"+scope+"-at-caret' "
    start = event.index(identifier)
    choice = event[start+len(identifier)+1:len(event)-1].lower().replace(" ", ".").strip()
    filetype = event.split(" ")[1].replace("-"+scope+"-at-caret'", "")[1:]
    base = "contextual."+scope
    options = option_is(languages, actions, base, filetype, choice)
    if len(options) != 1:
        return
    write("event|'"+scope+"-at-caret' '"+get_event(options[0])+"'")

def handle_event(global_profile, local_profile):
    tamper_actions = run_process(["oi", "conf", "read", "contextual.tamper.*"])
    navigate_actions = run_process(["oi", "conf", "read", "contextual.navigate.*"])
    command_actions = run_process(["oi", "conf", "read", "contextual.command.*"])
    languages = []
    languages_output = run_process(["oi", "conf", "read", "enabled.languages"])
    if len(languages_output) == 1:
        languages = languages_output[0].split(",")
    languages.append("any")
    while True:
        event = sys.stdin.readline().strip("\n")
        if event == "shutdown":
            break
        if event == None or event == "":
            time.sleep(0.1)

        if event.startswith("'user-selected' '"):
            if "-tamper-at-caret' " in event:
                handle_select(languages, tamper_actions, event, "tamper")
                continue
            if "-navigate-at-caret' " in event:
                handle_select(languages, navigate_actions, event, "navigate")
                continue
            if "-command-at-caret' " in event:
                handle_select(languages, command_actions, event, "command")
                continue

        if event.endswith(" 'command' 'tamper-at-caret'"):
            handle_command(languages, tamper_actions, event, "tamper")

        if event.endswith(" 'command' 'navigate-at-caret'"):
            handle_command(languages, navigate_actions, event, "navigate")

        if event.endswith(" 'command' 'command-at-caret'"):
            handle_command(languages, command_actions, event, "command")

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        write_react_patterns()
    else:
        handle_event(args[1], args[2])
