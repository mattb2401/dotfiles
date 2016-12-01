#!/usr/bin/env python
import sys, os, subprocess

def print_react_patterns():
    # Write one event pr line that this script will react to
    print("'evaluate-at-caret'")

def write(msg):
    sys.stdout.write(msg.encode('utf-8')+"\n")
    sys.stdout.flush()

def run_process(exe,workingDir=""):    
    if workingDir == "":
        workingDir = os.getcwd()
    content = ""
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=workingDir)
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode(encoding='utf-8').strip('\n').strip('\r')
        if line != "":
            content = content+"\n"+line
        if(retcode is not None):
            break
    return content.strip("\n")

def get_caret():
    lines = [];
    write("request|editor get-caret")
    while True:
        line = sys.stdin.readline().strip("\n");
        if line == "end-of-conversation":
            break;
        lines.append(line)
    if len(lines) == 0:
        return None,0,0,None
    caret = lines[0].split("|")
    return caret[0], int(caret[1]), int(caret[2]), lines[1:]

def split_evaluator(line):
    escape = "||ESCAPED||";
    prepared = line.replace("//", escape).replace("\"", "\\\"")
    for x in prepared.split("/"):
        yield x.replace(escape, "/")

def look_for_evaluator(line, content):
    prefix = "#!/oi/"
    prefix2 = "#!/singleline/oi/"
    lines = content[0:line]
    if lines != None:
        for x in reversed(lines):
            line = x.replace("/*", "").replace("*/", "").strip();
            if line.startswith(prefix):
                return ("multi", split_evaluator(line[len(prefix):]))
            if line.startswith(prefix2):
                return ("single", split_evaluator(line[len(prefix2):]))
    return None,None

def get_query(line, content):
    query = ""
    for x in reversed(content[0:line]):
        if x.strip(" \t") == "":
            break
        query = x+"\n"+query
    for x in content[line:]:
        if x.strip(" \t") == "":
            break
        query = query+x+"\n"
    return query.strip("\n").replace("\n", "||newline||").replace("'", "\\'").replace("\"", "'")
 
def handle_event(event, global_profile, local_profile, args):
    filename, line, column, content = get_caret()
    if filename != None:
        evaltype, evaluator = look_for_evaluator(line, content)
        if evaluator == None:
            return
        if evaltype == "multi":
            query = get_query(line, content)
        else:
            query = content[line-1]
        arguments = []
        arguments.append('oi')
        for e in evaluator:
            arguments.append(e)
        arguments.append(query)
        arguments.append("--raw")
        write(run_process(arguments))
        write("    ")

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
