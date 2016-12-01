#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        # Write one event pr line that this script will react to
        echo "'tamper-at-caret' 'php-use-type'\n";
        echo "'user-selected' 'php-add-use-statement' *\n";
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

if ($argv[1] === "'tamper-at-caret' 'php-use-type'") {
    $handler = new UseHandler();
    $matches = $handler->getUseMatches();
    if (count($matches) === 0)
        logLine("did not find any matches");
    foreach ($matches as $match) {
        logLine($match);
    }
    if (count($matches) === 1) {
        $handler->addUse($matches[0]);
        return;
    }
    askUserForTypes($matches);
} else if (strpos($argv[1], "'user-selected' 'php-add-use-statement' ") !== false) {
    $selection = substr($argv[1], 41, strlen($argv[1]) - 42);
    if ($selection === "user-cancelled")
        return;
    $handler = new UseHandler();
    $handler->addUse($selection);
}

function askUserForTypes($types) {
    $choices = "";
    foreach ($types as $type) {
        if ($choices !== "")
            $choices = $choices.',';
        $choices = $choices.$type;
    }
    echo 'command|editor user-select php-add-use-statement "'.$choices."\"\n";
}

function logLine($line) {
    //echo $line."\n";
}

class UseHandler
{
    private $caret;

    public function __construct() {
        $this->caret = $this->getCaret();
    }

    public function getUseMatches() {
        $caret = $this->caret;
        $word = $this->getWordUnderCaret($caret);
        logLine('found word '.$word);
        return $this->findTypes($word);
    }
    
    public function addUse($use) {
        logLine('adding use for '.$use);
        $caret = $this->caret;
        logLine('caret length '.count($caret));
        $response = $this->getInsertLine($caret);
        $insertLine = $response['line'];
        $uses = $response['uses'];
        $useStatement = 'use '.$use.';';
        if (in_array($useStatement, $uses, true)) {
            logLine("already found a using,, not adding");
            return;
        }
        $toinsert = [];
        if ($response['preceedingNL']) {
            logLine("got perceedingNL thing");
            array_push($toinsert, '');
        }
        array_push($toinsert, $useStatement);
        if ($response['trailingNL']) {
            logLine("got trailingNL thing");
            array_push($toinsert, '');
        }
        $tempFile = $this->writeToTempFile($toinsert);
        logLine("sending to editor on line ".(string)$insertLine);
        $command = 'editor insert "'.$tempFile.'" "'.explode('|', $caret[0])[0].'|'.(string)$insertLine."|0\"\n";
        echo 'command|'.$command;
    }

    private function getInsertLine($caret) {
        $preceedingNL = false;
        $trailingNL = true;
        $lastUse = -1;
        $nsPosition = -1;
        $lineToInsert = -1;
        $uses = [];
        for ($i=1; $i < count($caret); $i++) { 
            logLine($caret[$i]);
            $line = trim($caret[$i]);
            if (strpos($line, 'use ') === 0) {
                $lastUse = $i;
                $uses[] = trim($line);
            } else if ($line === "") {
                continue;
            } else if (strpos($line, '<?') === 0) {
                continue;
            } else if (strpos($line, 'namespace ') === 0) {
                $nsPosition = $i;
                continue;
            } else {
                if ($lastUse !== -1) {
                    $lineToInsert = $lastUse+1;
                } else if ($nsPosition >= 0) {
                    $lineToInsert = $nsPosition+1;
                    $preceedingNL = true;
                } else {
                    $lineToInsert = $i - 1;
                }
            }
        }
        return [
            'line' => $lineToInsert,
            'preceedingNL' => $preceedingNL,
            'trailingNL' => $trailingNL,
            'uses' => $uses
        ];
    }

    private function writeToTempFile($contents) {
        $filename = "";
        while (true) {
            $filename = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('insert_file', true) . '.insert';
            if (!file_exists($filename))
                break;
        }
        $file = fopen($filename, 'w');
        foreach ($contents as $line)
            fwrite($file, $line."\n");
        fclose($file);
        return $filename;
    }

    private function findTypes($word) {
        $request = "codemodel get-signatures \"name=".$word."\"\n";
        echo "request|".$request;
        $lines = [];
        while (true) {
            $line = readline();
            if ($line === 'end-of-conversation')
                break;
            if (strpos($line, 'php|signature|') === 0) {
                array_push($lines, explode('|', $line)[3]);
            }
        }
        return $lines;
    }

    private function getCaret() {
        echo "request|editor get-caret\n";
        $lines = [];
        while (true) {
            $line = readline();
            if ($line === 'end-of-conversation')
                break;
            array_push($lines, $line);
        }
        return $lines;
    }

    private function getWordUnderCaret($lines) {
        $position = explode('|', $lines[0]);
        $line = intval($position[1]);
        $column = intval($position[2]);
        $start = $this->getWordStart($lines[$line], $column);
        if ($start === -1)
            return;
        $end = $this->getwordEnd($lines[$line], $column);
        return trim(substr($lines[$line], $start, $end - $start));
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


