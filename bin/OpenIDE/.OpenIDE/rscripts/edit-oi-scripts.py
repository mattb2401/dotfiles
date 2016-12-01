#!/usr/bin/env python
import os, sys, subprocess

def print_react_patterns():
    print("'navigate-at-caret' 'edit-oi-scripts'")
    print("'user-selected' 'edit-oi-scripts' '*")

def run_process(exe,working_dir=""):    
    if working_dir == "":
        working_dir = os.getcwd()
    output = []
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=working_dir)
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode(encoding='windows-1252').strip('\n').strip('\r')
        if line != "":
            output.append(line)
        if(retcode is not None):
            break
    return output

def append(current, to_add):
    if current != "":
        current += ","
    return current+to_add

def handle_event(event, global_profile, local_profile, args):
    if event == "'navigate-at-caret' 'edit-oi-scripts'":
        output_script = run_process(["oi", "script", "-n"])
        output_rscript = run_process(["oi", "rscript"])
        output_packages = run_process(["oi", "package", "-n"])
        option_string_script = (''.join(map(lambda x: "Script "+x+',', output_script))).strip(",")
        option_string_rscript = (''.join(map(lambda x: "RScript "+x+',', output_rscript))).strip(",")
        option_string_package = (''.join(map(lambda x: "Package "+x+',', output_packages))).strip(",")
        option_string = append("", option_string_script)
        option_string = append(option_string, option_string_rscript)
        option_string = append(option_string, option_string_package)
        print("command|editor user-select edit-oi-scripts \""+option_string+"\"")
    elif event.startswith("'user-selected' 'edit-oi-scripts' '"):
        token = "'user-selected' 'edit-oi-scripts' '"
        selection = event[len(token):-1].split(" ")
        type = selection[0].lower()
        name = selection[1]
        run_process(["oi", type, "edit", name])

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        print_react_patterns()
    else:
        handle_event(args[1], args[2], args[3], args[4:])
