#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        # Write one event pr line that this script will react to
        echo "'navigate-at-caret' 'extract-specifications-current-file'\n";
        exit(); 
    }
}

echo "request|editor get-caret\n";
$lines = "/*\n#!/singleline/oi/goto\n*/\n\n";
$file = explode('|', readline())[0];
$linenum = 1;
while (true) {
    $line = readline();
    if ($line == "end-of-conversation") {
        break;
    }
    if (strpos(trim($line), "public function test") === 0) {
        $line = trim($line);
        $end = strpos($line, '(');
        $line = str_replace('_', ' ', substr($line, 20, $end - 20));
        $line = str_replace('_', ' ', $line);
        $line = str_replace(" then ", " then\n", $line);
        $line = str_replace("given", "\ngiven", $line);
        $line = str_replace(" when", "\nwhen", $line);
        $line = str_replace(" and ", " and\n", $line);

        $line = str_replace("given it ", "given_it ", $line);
        $line = str_replace(" it will ", " it will\n", $line);
        $line = str_replace(" it is ", " it is\n", $line);
        $line = str_replace("given_it ", "given it ", $line);

        $line = str_replace("so that ", "so that\n", $line);
        if ($line !== null) {
            $lines .= "\"".$file.':'.(string)$linenum."\"\n".$line."\n\n";
        }
    }
    $linenum++;
}

// Chose bat because of coloring of strings
$tmpfname = tempnam(sys_get_temp_dir(), 'spec-extractor').".bat";
$temp = fopen($tmpfname, 'w');
fwrite($temp, $lines);
fclose($temp);
echo "command|editor goto \"".$tmpfname."|1|1\"\n";
