#!/usr/bin/env python
import os,sys,shutil,urllib

def write(msg):
    sys.stdout.write(msg+"\n")
    sys.stdout.flush()

def print_definitions():
    write('''Various www commands|
        generate|"Commands for generating web stuff"
            spa|"Single page applications using jQuery and handlebars" end 
        end  
    ''')

def download_file(url, destination):
    write("downloading "+url)
    urllib.urlretrieve(url, destination)

def create_index_html(root):
    write("writing index.html")
    content = '''<!doctype html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/handlebars-v1.3.0.js"></script>
    <script type="text/javascript" src="js/index.js"></script>

    <script id="sampleTemplate" type="text/x-handlebars-template">
        {{data.hello}} {{data.world}}
    </script>
</head>
<body>
<div id="sample"></div>
</body>
</html>'''
    index_file = os.path.join(root, "index.html")
    f = open(index_file,'w')
    f.write(content)
    f.close()

def create_index_js(js):
    write("writing index.js")
    content = '''$(document).ready(function() {
    compileTemplate("#sampleTemplate", "#sample", {
        data: {
            "hello": "Hello",
            "world": "World!"
        }
    });
});

function compileTemplate(name, destination, data) {
    var source   = $(name).html();
    var template = Handlebars.compile(source);
    var html    = template(data);
    $(destination).html(html);
}'''
    index_file = os.path.join(js, "index.js")
    f = open(index_file,'w')
    f.write(content)
    f.close()

def create_index_css(css):
    write("writing index.css")
    index_file = os.path.join(css, "index.css")
    f = open(index_file,'w')
    f.close()

def run_command(run_location, global_profile, local_profile, args):
    js_dir = os.path.join(run_location, "js")
    css_dir = os.path.join(run_location, "css")
    os.mkdir(js_dir)
    os.mkdir(css_dir)
    download_file("http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", os.path.join(js_dir, "jquery.min.js"))
    download_file("http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars-v1.3.0.js", os.path.join(js_dir, "handlebars-v1.3.0.js"))
    create_index_css(css_dir)
    create_index_js(js_dir)
    create_index_html(run_location)

if __name__ == "__main__":
    args = sys.argv
    if len(args) > 1 and args[2] == 'get-command-definitions':
        print_definitions()
    else:
        run_command(args[1], args[2], args[3], args[4:])
