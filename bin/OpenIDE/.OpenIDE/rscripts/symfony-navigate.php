#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        # Write one event pr line that this script will react to
        echo "'navigate-at-caret' 'php-go-to-service-definition'\n";
        echo "'navigate-at-caret' 'php-go-to-nearest-configuration'\n";
        echo "'navigate-at-caret' 'php-go-to-nearest-routing'\n";
        echo "'navigate-at-caret' 'php-go-to-nearest-service-definition'\n";
        echo "'user-selected' 'php-go-service-definition' *\n";
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

if (strpos($argv[1], "'user-selected' 'php-go-service-definition' ") === 0) {
    $selection = substr($argv[1], 45, strlen($argv[1]) - 46);
    if ($selection === "user-cancelled")
        return;
    navigateFromService($selection);
} else if ($argv[1] === "'navigate-at-caret' 'php-go-to-service-definition'") {
    $handler = new DefinitionHandler();
    $match = $handler->getMatch();
    if ($match !== null) {
        navigateFromService($match);
    }
} else if ($argv[1] === "'navigate-at-caret' 'php-go-to-nearest-configuration'") {
    navigateToNearest('config*.yml');
} else if ($argv[1] === "'navigate-at-caret' 'php-go-to-nearest-routing'") {
    navigateToNearest(['routing*.yml']);
} else if ($argv[1] === "'navigate-at-caret' 'php-go-to-nearest-service-definition'") {
    navigateToNearest('services*.yml');
}

function navigateFromService($match) {
    $handler = new DefinitionHandler();
    $caret = $handler->getCaret();
    $position = $handler->findInServices($caret, 'class: '.$match);
    if ($position !== null)
        echo 'command|editor goto "'.$position['file'].'|'.$position['line'].'|'.$position['column'].'"'."\n";
}

function navigateToNearest($match) {
    $handler = new DefinitionHandler();
    if (is_array($match)) {
        $files = [];
        foreach ($match as $filepattern) {
            $current = findFiles($filepattern);
            $files = array_merge($files, $current);
        }
    } else {
        $files = findFiles($match);
    }
    $caret = $handler->getCaret();
    if (count($caret) === 0)
        return false;
    $closest = getClosestFile($files, explode('|', $caret[0])[0]);
    if ($closest === null)
        return false;
    echo 'command|editor goto "'.$closest.'|1|1"'."\n";
    return true;
}

function getClosestFile($files, $templateFile) {
    $current = null;
    $diffPoint = 0;
    $isVendor = strpos($templateFile, '/vendor/') !== false;
    foreach ($files as $file) {
        $isTempFile = strpos($file, '.sw') === strlen($file) - 4;
        if ($isTempFile)
            continue;
        if (!$isVendor && strpos($file, './vendor/') === 0)
            continue;
        $fullpath = realpath($file);
        for ($i=0; $i < strlen($templateFile); $i++) {
            if (strlen($fullpath) < $i + 1)
                break;
            if ($fullpath[$i] === $templateFile[$i]) {
                if ($diffPoint < $i) {
                    $current = $fullpath;
                    $diffPoint = $i;
                }
            } else {
                break;
            }
        }
    }
    return $current;
}

function findFiles($name) {
    exec('oi locate "'.$name.'"', $output);
    return $output;
}

class DefinitionHandler
{
    public function getMatch() {
        $caret = $this->getCaret();
        // Find class definition
        $word = $this->getWordUnderCaret($caret, $this->getOperators());
        $uses = $this->getUses($caret);
        foreach ($uses as $use) {
            if ($this->endsWith($use['alias'], "\\".$word))
                return $use['name'];
        }

        // Find full class namespace from string
        $ns = $this->getWordUnderCaret($caret, $this->getNSOperators());
        if ($ns !== $word) {
            return $ns;
        }

        // Find any class matching word
        return $this->chooseAllMatches($word);
    }

    public function findInServices($caret, $phrase) {
        exec('oi locate "services.yml"', $output);
        if (count($caret) !== 0) {
            $closest = $this->getClosestFile($output, explode('|', $caret[0])[0]);
            if ($closest !== null) {
                $location = $this->matchInFile($closest, $phrase);
                if ($location !== null)
                    return $location;
            }
        }
        foreach ($output as $file) {
            $location = $this->matchInFile($file, $phrase);
            if ($location !== null)
                return $location;
        }
        exec('oi locate "services_app.yml"', $output);
        if (count($caret) !== 0) {
            $closest = $this->getClosestFile($output, explode('|', $caret[0])[0]);
            if ($closest !== null) {
                $location = $this->matchInFile($closest, $phrase);
                if ($location !== null)
                    return $location;
            }
        }
        foreach ($output as $file) {
            $location = $this->matchInFile($file, $phrase);
            if ($location !== null)
                return $location;
        }
        return null;
    }

    public function matchInFile($file, $phrase) {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $lineNr = 1;
        foreach ($lines as $line) {
            if ($phrase === trim($line, " \t")) {
                return [
                    'file' => realpath($file),
                    'line' => (string)($lineNr+1),
                    'column' => '0'
                ];
            }
            $lineNr++;
        }
        return null;
    }

    public function getClosestFile($files, $templateFile) {
        $current = null;
        $diffPoint = 0;
        foreach ($files as $file) {
            $fullpath = realpath($file);
            for ($i=0; $i < strlen($templateFile); $i++) {
                if (strlen($fullpath) < $i + 1)
                    break;
                if ($fullpath[$i] === $templateFile[$i]) {
                    if ($diffPoint < $i) {
                        $current = $fullpath;
                        $diffPoint = $i;
                    }
                } else {
                    break;
                }
            }
        }
        return $current;
    }

    public function chooseAllMatches($word) {
        $matches = $this->findTypes($word);
        if (count($matches) === 0)
            return null;
        if (count($matches) === 1)
            return $matches[0];
        $matchList = "";
        foreach ($matches as $match) {
            if ($matchList !== "")
                $matchList = $matchList.',';
            $matchList = $matchList.$match;
        }
        echo 'command|editor user-select "php-go-service-definition" "'.$matchList."\"\n";
        return null;
    }

    public function getUses($caret) {
        $uses = [];
        for ($i=1; $i < count($caret); $i++) {
            $line = trim($caret[$i], " \t");
            if (strpos($line, 'use ') === 0) {
                $use = substr($line, 4, strlen($line) - 5);
                $useChunks = explode(' ', $use);
                if (count($useChunks) === 1) {
                    $uses[] = ['name' => $use, 'alias' => $use];
                } else {
                    if (count($useChunks) === 3) {
                        if (strtolower($useChunks[1]) === 'as') {
                            $pathChunks = explode("\\", $useChunks[0]);
                            $basepath = substr($useChunks[0], 0, strlen($useChunks[0]) - strlen($pathChunks[count($pathChunks)-1]));
                            $uses[] = ['name' => $useChunks[0], 'alias' => $basepath."\\".$useChunks[2]];
                        }
                    }
                }
            } else if ($line === "") {
                continue;
            } else if (strpos($line, '<?') === 0) {
                continue;
            } else if (strpos($line, 'namespace ') === 0) {
                continue;
            } else {
                break;
            }
        }
        return $uses;
    }

    public function findTypes($word) {
        $request = "codemodel get-signatures \"name=".$word."\"\n";
        echo "request|".$request;
        $lines = [];
        while (true) {
            $line = readln();
            if ($line === 'end-of-conversation')
                break;
            if (strpos($line, 'php|signature|') === 0) {
                array_push($lines, explode('|', $line)[3]);
            }
        }
        return $lines;
    }

    public function getCaret() {
        echo "request|editor get-caret\n";
        $lines = [];
        while (true) {
            $line = readln();
            if ($line === 'end-of-conversation')
                break;
            array_push($lines, $line);
        }
        return $lines;
    }

    public function getWordUnderCaret($lines, $operators) {
        $position = explode('|', $lines[0]);
        $line = intval($position[1]);
        $column = intval($position[2]);
        $start = $this->getWordStart($lines[$line], $column, $operators);
        if ($start === -1)
            return;
        $end = $this->getwordEnd($lines[$line], $column, $operators);
        return trim(substr($lines[$line], $start, $end - $start));
    }

    public function getWordStart($line, $column, $operators) {
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

    public function getWordEnd($line, $column, $operators) {
        for ($i=$column; $i < strlen($line) + 1; $i++) {
            if (in_array($line[$i - 1], $operators, true))
                return $i - 1;
        }
        return strlen($line);
    }

    public function getDINameOperators() {
        return ['{','}','[',']','(',')',',',"'",'"','+','-','/','\\','>','<','*','^','=','!','&',':',';',' ',"\n",'@'];
    }

    public function getNSOperators() {
        return ['{','}','[',']','(',')','.',',',"'",'"','+','-','/','>','<','*','^','=','!','&',':',';',' ',"\n",'@'];
    }

    public function getOperators() {
        return ['{','}','[',']','(',')','.',',',"'",'"','+','-','/','\\','>','<','*','^','=','!','&',':',';',' ',"\n",'@'];
    }

    function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}

function readln() {
    return trim(fgets(STDIN), PHP_EOL);
}
