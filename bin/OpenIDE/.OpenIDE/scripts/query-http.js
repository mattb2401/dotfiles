#!/usr/bin/env node
// Script parameters
//  Param 1: Script run location
//  Param 2: global profile name
//  Param 3: local profile name
//  Param 4-: Any passed argument
//
// When calling oi use the --profile=PROFILE_NAME and 
// --global-profile=PROFILE_NAME argument to ensure calling scripts
// with the right profile.
//
// To post back oi commands print command prefixed by command| to standard output
// To post a comment print to std output prefixed by comment|
// To post an error print to std output prefixed by error|

if (process.argv.length > 2) {
    if (process.argv[2] === "get-command-definitions") {
        // Definition format usually represented as a single line:

        // Script description|
        // command1|"Command1 description"
        //  param|"Param description" end
        // end
        // command2|"Command2 description"
        //  param|"Param description" end
        // end

        console.log("Queries any http endpoint|");
        console.log('QUERYTEXT|"For instance GET /some/url. User ||newline|| for multiline strings" end ');
        process.exit();
    }
}

var http = require("http");
var https = require("https");
var fs = require("fs");
var urlLib = require("url");

var headers = {
    "content-type": "application/json",
    "accept": "application/json"
};
var queryPosition = 5;
if (process.argv.length === 7) {
    queryPosition = 6;
    headers = JSON.parse(process.argv[5]);
}

var query = "";
for (var i = queryPosition; i < process.argv.length; i++) {
    query += process.argv[i].replace(/\|\|newline\|\|/g, "\n")+" ";
}
var lines = query.split("\n")
var firstLine = getFirstLine(lines);
if (firstLine === null) {
    process.exit();
}
var chunks = firstLine.split(" ");
if (chunks.length < 2) {
    process.exit();
}
var verb = chunks[0];
var url = chunks[1].trimLeft("/");
var requestBody = "";
var bodyStart = query.indexOf(url)+url.length;
if (bodyStart < query.length) {
    if (bodyStart > 0) {
        requestBody = query.substring(bodyStart+1).replace(/'/g, "\"");
        lines = requestBody.split("\n");
        requestBody = "";
        for (var i = 0; i < lines.length; i++) {
             if (lines[i].trim().indexOf("//") === 0) {
                continue;
             }
             if (lines[i].trim() === "") {
                continue;
             }
             if (lines[i].trim().indexOf("|") === 0) {
                break;
             }
             requestBody += lines[i]+"\n";
        };
        if (requestBody.length != 0) {
            JSON.parse(requestBody);
        }
    }
}

var resultProcessor = "";
var appendPreprocessorLine = false;
for (var i = 0; i < lines.length; i++) {
     if (lines[i].trim().indexOf("//") === 0) {
        continue;
     }
     if (lines[i].trim() === "") {
        continue;
     }
     if (lines[i].trim().indexOf("|") === 0) {
        appendPreprocessorLine = true;
        continue;
     }
     if (appendPreprocessorLine) {
        resultProcessor += lines[i]+"\n";
     }
};

console.log("color|DarkYellow|"+verb+" "+url);
var options = urlLib.parse(url);
options.method = verb;
options.headers = headers;

var onResponse = function (res) {
    var body = "";
    if (res.statusCode > 199 && res.statusCode < 400) {
        console.log("color|Green|" + res.statusCode +" "+http.STATUS_CODES[res.statusCode]);
    } else if (res.statusCode > 399) {
        console.log("color|Red|" + res.statusCode +" "+http.STATUS_CODES[res.statusCode]);
    } else {
        console.log(res.statusCode +" "+http.STATUS_CODES[res.statusCode]);
    }
    res.on('data', function (chunk) {
        body += chunk;
    });
    if (res.statusCode === 302) {
        console.log(prettify(JSON.stringify(res.headers)));
    }
    res.on('end', function () {
        var ms = Date.now() - start;
        var seconds = Math.floor(ms / 1000);
        ms = ms - (seconds*100);
        var json = function () {
            return JSON.parse(body);
        };

        if (resultProcessor.length > 0) {
            resultProcessor = "function preProcess(body) {\n" + resultProcessor + "}\npreProcess(body);";
            try {
                var preprocessed = eval(resultProcessor);
                prettify(preprocessed);
            } catch (ex) {
                console.log(body);
            }
        } else {
            prettify(body);
        }

        console.log('requests.time', seconds.toString()+"."+ms.toString());
        console.log('');
    });
};


try {
    var start = Date.now();
    var req = null;
    if (url.indexOf("https") === 0) {
        req = https.request(options, onResponse)
    } else {
        req = http.request(options, onResponse)
    }
    if (verb !== "GET" && requestBody.trim() !== "") {
        req.write(requestBody);
    }
    req.end();
} catch (ex) {
    console.log(ex)
    console.log("It all went tits up");
}

function getFirstLine(lines) {
    for (var i = 0; i < lines.length; i++) {
         if (lines[i].trim().indexOf("//") === 0) {
            continue;
         }
         return lines[i];
    };
    return null;
}

function prettify(buffer) {
    if (typeof(buffer) === 'object') {
        console.log(JSON.stringify(buffer, null, 2));
        return;
    }
    if (buffer.slice(0,5) === "HTTP/") {
        var index = buffer.indexOf('\r\n\r\n');
        var sepLen = 4;
        if (index == -1) {
            index = buffer.indexOf('\n\n');
            sepLen = 2;
        }
        if (index != -1) {
            console.log(buffer.slice(0, index+sepLen));
            buffer = buffer.slice(index+sepLen);
        }
    }
    if (buffer[0] === '{' || buffer[0] === '[') {
        try {
            console.log(JSON.stringify(JSON.parse(buffer), null, 2));
            console.log('\n');
        } catch(ex) {
            console.log(buffer);
            if (buffer[buffer.length-1] !== "\n") {
                console.log('\n');
            }
        }
    } else {
        console.log(buffer);
        if (buffer[buffer.length-1] !== "\n") {
            console.log('\n');
        }
    }
}

