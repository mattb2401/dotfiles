#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        echo "'navigate-at-caret' 'symfony-route-navigation'\n";
        echo "'user-selected' 'symfony-route-navigation' '*\n";
        exit(); 
    }
}

if (strpos($argv[1], "'user-selected' 'symfony-route-navigation' '") !== false) {
    $match = "'user-selected' 'symfony-route-navigation' '";
    $selection = substr($argv[1], strlen($match), strlen($argv[1]) - strlen($match) - 1);
    if ($selection === "user-cancelled")
        return;
    $line = explode(':', $selection);
    $class = $line[0]."\\Controller\\".$line[1].'Controller';
    $class = str_replace('Melin', "Melin\\", $class);
    $match = positionFromSignature($class);
    if ($match !== null) {
        $lines = file($match['file']);
        $lineNum = $match['line'];
        $functionToken = ' function '.$line[2].'Action(';
        for ($i=0; $i < count($lines); $i++) { 
            if (strpos($lines[$i], $functionToken) !== false) {
                $lineNum = $i + 2;
                break;
            }
        }
        echo "command|editor goto \"".$match['file'].'|'.$lineNum."|0\"\n";
    }
    exit;
}

exec('backend/app/console debug:router --show-controllers', $output);
$lines = array_reduce(
    array_filter(
        array_map(function ($line) {
            $chunks = array_values(array_filter(explode(" ", $line)));
            if (count($chunks) === 6 && strpos($chunks[4], '/') === 0) {
                return [
                    $chunks[1],
                    $chunks[4],
                    $chunks[5]
                ];
            }
            return null;
        }, $output),
        function ($itm) {
            if ($itm === null) {
                return false;
            }
            return $itm[0] !== "ANY" && strpos($itm[1], '/_') !== 0;
        }
    ),
    function ($all, $itm) {
        if ($all !== '') {
            $all .= ',';
        }
        return $all.$itm[2].'||'.$itm[0].' '.$itm[1];
    },
    ""
);
echo 'command|editor user-select symfony-route-navigation "'.$lines.'"'."\n";


function positionFromSignature($use) {
    $request = "codemodel get-signatures \"signature=".$use."\"\n";
    echo "request|".$request;
    $file = "";
    while (true) {
        $line = readline();
        if ($line === 'end-of-conversation') {
            break;
        }

        if (strpos($line, 'file|') === 0) {
            $file = explode('|', $line)[1];
            continue;
        }
        if (strpos($line, 'php|signature|') === 0) {
            $chunks = explode('|', $line);
            return [
                'file'   => $file,
                'line'   => $chunks[7],
                'column' => $chunks[8]
            ];
        }
    }
    return null;
}
