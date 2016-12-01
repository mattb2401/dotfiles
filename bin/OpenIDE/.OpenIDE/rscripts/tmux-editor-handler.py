#!/usr/bin/env python
import os, sys, tempfile, subprocess

util_path = os.path.join(os.path.dirname(os.path.realpath(__file__)), "tmux-editor-handler-files")

def run_process(exe,working_dir=""):
    if working_dir == "":
        working_dir = os.getcwd()
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=working_dir)
    lines = []
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode().strip('\n')
        lines.append(line)
        if(retcode is not None):
            break
    return lines

def print_react_patterns():
    # Write one event pr line that this script will react to
    print("'user-select' 'unsupported' '*")
    print("'user-input' 'unsupported' '*")
    
def handle_event(event, global_profile, local_profile, args):
    # Write scirpt code here.
    if event.startswith("'user-select' 'unsupported' '"):
        chunks = event.split(" ")
        token = chunks[2].strip("'")
        options = ""
        for chunk in chunks[3:]:
            options = options+" "+chunk
        options = options.strip(" ").strip("'")
        lncount = 3
        f = tempfile.NamedTemporaryFile(delete=False)
        for line in options.split(","):
            f.write(line+os.linesep)
            lncount = lncount + 2
        f.close()
        proc = [os.path.join(util_path, "user-select-launcher"), os.getcwd(), token, f.name, str(lncount)]
        run_process(proc)
    if event.startswith("'user-input' 'unsupported' '"):
        chunks = event.split(" ")
        token = chunks[2].strip("'")
        
        options = ""
        for chunk in chunks[3:]:
            options = options+" "+chunk
        options = options.strip(" ").strip("'")
        proc = [os.path.join(util_path, "user-input-launcher"), os.getcwd(), token, options]
        run_process(proc)

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
