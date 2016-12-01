#!/usr/bin/env python
import sys, tempfile

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

def print_react_patterns():
    # Write one event pr line that this script will react to
    print("'tamper-at-caret' 'php-sentence-to-test'")
    
def handle_event(event, global_profile, local_profile, args):
    # Write scirpt code here.
    filename, line, column, lines = get_caret()
    name = lines[line-1].strip()
    test = "    public function test"+name.capitalize().replace(" ", "_")+"()\n    {\n        $this->assertFalse(true);\n    }"
    file = tempfile.NamedTemporaryFile(delete = False)
    file.write(test)
    file.close()
    print("command|editor replace \""+file.name+"\" \" \""+filename+"|"+str(line)+"|0\" \""+str(line)+"|"+str(len(lines[line-1])+1)+"\"")
    print("command|editor goto \""+filename+"|"+str(line+1)+"|5\"")

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        print_react_patterns()
    else:
        handle_event(args[1], args[2], args[3], args[4:])
