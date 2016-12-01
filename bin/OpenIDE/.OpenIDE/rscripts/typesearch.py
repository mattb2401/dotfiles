#!/usr/bin/env python
import sys

def print_react_patterns():
    print("'gototype-extension'")
    print("'user-inputted' 'gototype-extension' '*")
    print("'user-selected' 'typesearch' '*")
    
def write(msg):
    sys.stdout.write(msg+"\n")
    sys.stdout.flush()

def handle_event(event, global_profile, local_profile, args):
    if (event.startswith("'user-inputted' 'gototype-extension' '")):
        token = "'user-inputted' 'gototype-extension' '"
        selection = event[len(token):-1]
        write("request|codemodel find-types \""+selection+"\" 100")
        result = []
        while  True:
            line = sys.stdin.readline().strip("\n")
            if line == "end-of-conversation":
                break
            result.append(line)
        options = ""
        current_file = None
        match_count = 0
        for line in result:
            chunks = line.split("|")
            if line.startswith("file|"):
                current_file = chunks[1]
                continue
            if len(chunks) < 9:
                continue
            match_count = match_count +1
            options += current_file+"|"+chunks[7]+"|"+chunks[8]+"||"+chunks[3]+","
        if match_count == 1:
            write("command|editor goto \""+options.split("||")[0]+"\"")
        else:
            write("command|editor user-select typesearch \""+options.strip(",")+"\"")
        return
    if (event.startswith("'user-selected' 'typesearch' '")):
        token = "'user-selected' 'typesearch' '"
        selection = event[len(token):-1]
        if selection == "user-cancelled":
            return
        write("command|editor goto \""+selection+"\"")
        return
    write("command|editor user-input gototype-extension")

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        print_react_patterns()
    else:
        handle_event(args[1], args[2], args[3], args[4:])
