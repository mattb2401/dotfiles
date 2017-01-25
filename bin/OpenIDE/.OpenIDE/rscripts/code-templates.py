#!/usr/bin/env python
import os,sys,tempfile

def print_react_patterns():
    print("'tamper-at-caret' 'code-template-insert'")
    print("'user-selected' 'code-template-insert' '*")

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

def editor_insert(filename, line, column, content):
    file = tempfile.NamedTemporaryFile(delete = False)
    file.write(content)
    file.close()
    print("command|editor insert \""+file.name+"\" \""+filename+"|"+str(line)+"|"+str(column)+"\"")
 
def handle_event(event, global_profile, local_profile, args):
    options = {
        "allFiles": "Http query at caret,Mysql query at caret",
        ".cs": "Nancy Selfhost,Nancy Module",
        ".js": "Node Readline,Node Websocket Server,Node block under caret,Websocket client,Change CSS rule in dom",
        ".py": "Python Run Process,Python Get Caret,Python Editor Insert",
        ".sh": "Bash CI,xbuild",
        ".php": "Php public function,Php protected function,Php private function,Php class,Php class with constructor"
    }
    if event == "'tamper-at-caret' 'code-template-insert'":
        filename, _, _, _ = get_caret()
        extension = os.path.splitext(os.path.basename(filename))[1]
        if extension in options:
            all = options['allFiles'] + "," + options[extension]
            print("command|editor user-select code-template-insert \""+all+"\"")

    if event.startswith("'user-selected' 'code-template-insert' '"):
        token = "'user-selected' 'code-template-insert' '"
        selection = event[len(token):-1]
        content = None
	if selection == "Http query at caret":
	    content = at_caret_http()
	if selection == "Mysql query at caret":
	    content = at_caret_mysql()
        if selection == "Nancy Selfhost":
            content = nancy_selfhost()
        if selection == "Nancy Module":
            content = nancy_module()
        if selection == "Node Readline":
            content = node_readline()
        if selection == "Node Websocket Server":
            content = node_websocket_server()
        if selection == "Node block under caret":
            content = node_read_caret_block()
        if selection == "Websocket client":
            content = js_websocket_client()
        if selection == "Change CSS rule in dom":
            content = js_change_css_rule_in_dom()
        if selection == "Python Run Process":
            content = python_runprocess()
        if selection == "Python Get Caret":
            content = python_getcaret()
        if selection == "Python Editor Insert":
            content = python_editor_insert()
        if selection == "Bash CI":
            content = bash_ci()
        if selection == "xbuild":
            content = bash_xbuild()
        if selection == "Php public function":
            content = php_function('public')
        if selection == "Php protected function":
            content = php_function('protected')
        if selection == "Php private function":
            content = php_function('private')
        if selection == "Php class":
            content = php_class()
        if selection == "Php class with constructor":
            content = php_class_with_constructor()

        if content != None:
            filename, line, column, caret = get_caret()
            editor_insert(filename, line, column, content)

def at_caret_http():
    return '''/* #!/oi/query-http/{"Authorization": "Bearer f1973463f8df3a4a78d2a1f595404b405c6ce0c0","Ps-Worker-ApiKey":"123456789","Ps-Worker-Version":"0","Content-Type": "application//json"}

GET http://db.local.gl:9200/demographic/person/_search
|
return JSON.parse(body);

*/'''

def at_caret_mysql():
    return '''/* #!/oi/query-mysql/db.local.gl/root/qwerty1234 */'''

def bash_xbuild():
    return '''BINARYDIR=$1/build_output
if [ -d $BINARYDIR ]; then
    rm -r $BINARYDIR/
fi
xbuild replace.csproj /target:rebuild /property:OutDir=$BINARYDIR/ /p:Configuration=Release;'''

def php_function(visibility):
    return visibility + ''' function ()
{
}'''

def php_class():
    return '''class 
{
}'''

def php_class_with_constructor():
    return '''class 
{
    public function __construct()
    {
    }
}'''

def bash_ci():
    return '''OUTPUT=$(xbuild src/Demo/Demo.csproj)
if [[ "$OUTPUT" == *error* ]]; then
    echo $OUTPUT|grep error|xargs echo "error|"
    echo ""
    echo "event|build failed Demo.csproj"
    notify-send "Build failed"
else
    echo "event|build completed Demo.csproj"
    notify-send "$(src/Demo/bin/Debug/Demo.exe)"
fi'''

def python_editor_insert():
    return '''def editor_insert(filename, line, column, content):
    file = tempfile.NamedTemporaryFile(delete = False)
    file.write(content)
    file.close()
    print("command|editor insert \""+file.name+"\" \""+filename+"|"+str(line)+"|"+str(column)+"\"")'''

def python_getcaret():
    return '''def get_caret():
    output = [];
    sys.stdout.write("request|editor get-caret\\n")
    sys.stdout.flush()
    while True:
        line = sys.stdin.readline().strip("\\n")
        if line == "end-of-conversation":
            break;
        output.append(line)
    caret = output[0].split("|")
    return caret[0], int(caret[1]), int(caret[2]), output[1:]'''

def python_runprocess():
    return '''def run_process(exe,working_dir=""):
    if working_dir == "":
        working_dir = os.getcwd()
    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, cwd=working_dir)
    lines = []
    while(True):
        retcode = p.poll() # returns None while subprocess is running
        line = p.stdout.readline().decode().strip('\\n')
        lines.append(line)
        if(line == "" and retcode is not None):
            break
    return lines'''

def node_websocket_server():
    return '''var WebSocketServer = require('ws').Server,
    wss = new WebSocketServer({port: 8081});

wss.broadcast = function(data) {
    for(var i in this.clients)
        this.clients[i].send(data);
};'''

def node_readline():
    return '''var readline = require('readline');

var rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
    terminal: false
});

rl.on('line', function (line) {

});'''

def node_read_caret_block():
    return '''function getExpression(caret) {
    var expression = "";
    for (var i = caret.line - 1; i >= 0; i--) {
        line = caret.content[i];
        if (line.trim() === "") {
            break;
        }
        expression = line+"\\n" + expression;
    };
    for (var i = caret.line; i < caret.content.length; i++) {
        line = caret.content[i];
        if (line.trim() === "") {
            break;
        }
        expression = expression + line+"\\n";
    };
    return {
        rule: expression,
        name: expression.substring(0, expression.indexOf("{")).trim(),
        stylesheet: path.basename(caret.file)
    };
}'''

def js_websocket_client():
    return '''var client = new WebSocket("ws://127.0.0.1:8081/");
client.onopen = function () {
    console.log("Connected")
};
client.onmessage = function (event) {
    var body = JSON.parse(event.data);
    changeRule(body.stylesheet, body.name, body.rule);
};
client.onclose = function () {
    console.log("Disconnecting");
};'''

def js_change_css_rule_in_dom():
    return '''function changeRule(sheetname, rulename, content) {
    for (var i = 0; i < document.styleSheets.length; i++) {
        var stylesheet = document.styleSheets[i];
        if (stylesheet.href.indexOf(sheetname) > -1) {
            for (var a = 0; a < stylesheet.cssRules.length; a++) {
                if (stylesheet.cssRules[a].selectorText == rulename) {
                    stylesheet.deleteRule(a);
                    stylesheet.insertRule(content, 0);
                    return;
                }
            };
            stylesheet.insertRule(content, 0);
            return;
        }
    };
}'''

def nancy_module():
    return '''public class TemplateModule : NancyModule
    {
        public TemplateModule()
        {
            Get["/"] = parameters => Response.AsJson(new { hello = "template" });
        }
    }'''

def nancy_selfhost():
    return '''using (var host = new NancyHost(new Uri("http://localhost:1234")))
            {
                host.Start();
                Console.WriteLine("Server started on http://localhost:1234");
                Console.ReadLine();
            }'''

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[1] == 'reactive-script-reacts-to':
        print_react_patterns()
    else:
        handle_event(args[1], args[2], args[3], args[4:])
