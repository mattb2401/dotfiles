#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        echo "'.php' 'command' 'new-from-caret'\n";
        echo "'user-selected' 'php-caret-create-type-select' *\n";
        echo "'user-inputted' 'php-caret-create-input' *\n";
        exit(); 
    }
}

# Write scirpt code here.
#   Param 1: event
#   Param 2: global profile name
#   Param 3: local profile name
#
# When calling other commands use the --profile=PROFILE_NAME and 
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.
$desired = "'user-inputted' 'php-caret-create-input' '";
if (strpos($argv[1], $desired) === 0) {
    $value = getLastItem($argv[1], $desired);
    if ($value === "user-cancelled") {
        return;
    }
    $fileStart = strpos($value, ' ');
    $type = substr($value, 0, $fileStart);
    $value = substr($value, $fileStart+1);
    if (strpos($value, '.php') !== strlen($value) - 4) {
        $value .= '.php';
    }
    $value = str_replace(getcwd().'/', '', $value);
    exec('oi php new '.$type.' "'.$value.'"');
    return;
}
$desired = "'user-selected' 'php-caret-create-type-select' '";
if (strpos($argv[1], $desired) === 0) {
    $value = getLastItem($argv[1], $desired);
    if ($value === "user-cancelled") {
        return;
    }
    $caret = new Caret();
    $dir = dirname($caret->file);
    echo 'command|editor user-input php-caret-create-input "'.$value.' '.$dir.'/"'."\n";
    return;
}
$types = str_replace(' ', ',', exec('oi get-commands php new'));
echo 'command|editor user-select php-caret-create-type-select "'.$types.'"'."\n";


class Caret
{
    public $file = '';
    public $line = 1;
    public $column = 1;

    public $lines = [];

    public function __construct() {
        echo "request|editor get-caret\n";
        $rawCaret = [];
        while (true) { 
            $line = readln();
            if ($line === "end-of-conversation") {
                break;
            }
            $rawCaret[] = $line;
        }

        $chunks = explode('|', $rawCaret[0]);
        $this->file = $chunks[0];
        $this->line = intval($chunks[1]);
        $this->column = intval($chunks[2]);
        for ($i=1; $i < count($rawCaret); $i++) { 
            $this->lines[] = $rawCaret[$i];
        }
    }
    
    public function wordAtCaret() {
        $line = $this->lines[$this->line - 1];
        $start = $this->getWordStart($line, $this->column);
        if ($start === -1)
            return;
        $end = $this->getwordEnd($line, $this->column);
        return trim(substr($line, $start, $end - $start));
    }

    private function getWordStart($line, $column) {
        $operators = $this->getOperators();
        $startAt = $column - 1;
        // If we are at the start of the word jump one back 
        if (in_array($line[$startAt], $operators, true))
            $startAt = $startAt - 1;
        for ($i=$startAt; $i >= 1; $i--) { 
            if (in_array($line[$i - 1], $operators, true))
                return $i;
        }
        return -1;
    }

    private function getWordEnd($line, $column) {
        $operators = $this->getOperators();
        for ($i=$column; $i < strlen($line) + 1; $i++) { 
            if (in_array($line[$i - 1], $operators, true))
                return $i - 1;
        }
        return strlen($line);
    }

    private function getOperators() {
        return ['{','}','[',']','(',')','.',',',"'",'"','+','-','/','\\','>','<','*','^','=','!','&',':',';',' ',"\n",'@'];
    }
}

function getLastItem($event, $match) {
    return substr($event, strlen($match), strlen($event)-strlen($match)-1);
}

function readln() {
    return trim(fgets(STDIN), PHP_EOL);
}
