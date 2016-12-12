#!/usr/bin/env php
<?php
$replPattern = "'.php' 'command' 'repl' '";
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        # Write one event pr line that this script will react to
        echo "run-as-service\n";
        echo $replPattern."*";
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

$linesToEvaluate = ["<?php"];
while ($line = readline()) {
    if ($line === 'shutdown') {
        break;
    }
    $code = "";
    $textToEvaluateAsCode = substr($line, strlen($replPattern), strlen($line) - (strlen($replPattern) + 1));
    $textToEvaluateAsCode = str_replace("||q||", "\"", $textToEvaluateAsCode);
    $textToEvaluateAsCode = str_replace("||sq||", "'", $textToEvaluateAsCode);
    $textToEvaluateAsCode = str_replace("||dollar||", "$", $textToEvaluateAsCode);
    if ($textToEvaluateAsCode === 'clearOIRepl') {
        $linesToEvaluate = ["<?php"];
    } else {
        $selectionLines = explode("||newline||", $textToEvaluateAsCode); 
        $linesToRun = getNewMergedLines($linesToEvaluate, $selectionLines);
        foreach ($linesToRun as $ln) {
            $code .= $ln."\n";
        }

        $file = "/tmp/php_repl_content.php";
        file_put_contents($file, $code);
        $output = "";
        $dir = exec('pwd');
        //$program = 'docker run -v '.$dir.':/rundir -v '.$file.':/tmp/script.php e-portal-dev bash -c "cd /rundir && php /tmp/script.php"';
        //$err = exec($program, $output, $procReturn);
        $err = exec("php ".$file, $output, $procReturn);
        if ($procReturn === 0) {
            echo $code."\n";
            foreach ($output as $ol) {
                echo $ol."\n";
            }
            $linesToEvaluate = getNewMergedLines($linesToEvaluate, $selectionLines);
        } else {
            echo "error|".$code."\n";
            echo "error|".$err."\n";
        }
    }
}

function fullTrim($str) {
    return trim($str, "\t ");
}

function scanToEndBracket($lines, $startFrom) {
    $startFound = false;
    $end = null;
    $level = 0;
    for ($i = $startFrom; $i < count($lines); $i++) {
        $chars = str_split($lines[$i]);
        foreach ($chars as $char) {
            if (!$startFound) {
                if ($char === "{") {
                    $startFound = true;
                }
                continue;
            }
            if ($char === "{") {
                $level++;
                continue;
            }
            if ($char === "}" && $level === 0) {
                $end = $i;
                break;
            } else if ($char === "}") {
                $level--;
            }
        }
        if ($end !== null) {
            break;
        }
    }
    return $end;
}

function mergeNewLines($lines, $newLines, $start, $end) {
    $merged = [];
    if ($start === null || $end === null) {
        foreach ($lines as $line) {
            $merged[] = $line;
        }
        foreach ($newLines as $line) {
            $merged[] = $line;
        }
    } else {
        for ($i = 0; $i < count($lines); $i++) {
            if ($i === $start) {
                foreach ($newLines as $line) {
                    $merged[] = $line;
                }
            } else {
                if ($i > $start && $i <= $end) {
                    continue;
                }
                $merged[] = $lines[$i];
            }
        }
    }
    return $merged;
}

function matchStartEndFor($lines, $trimmedNewLine, $match, $scanFromChar) {
    $foundAt = strpos($trimmedNewLine, $match, 0);
    if ($foundAt === 0) {
        $paramStart = strpos($trimmedNewLine, $scanFromChar, strlen($match));
        if ($paramStart > 0) {
            $trimmedMatchPart = substr($trimmedNewLine, 0, $paramStart);
            for ($i = 0; $i < count($lines); $i++) {
                $line = $lines[$i];
                $trimmedLine = fullTrim($line);
                if (strpos($trimmedLine, $trimmedMatchPart, 0) === 0) {
                    $endLine = scanToEndBracket($lines, $i);
                    if ($endLine !== null) {
                        return [
                            'start' => $i,
                            'end' => $endLine
                        ];
                    }
                }
            }
        }
    }
    return null;
}

function getNewMergedLines($lines, $newLines) {
    $appendStart = null;
    $appendEnd = null;
    foreach ($newLines as $newLine) {
        $trimmedNewLine = fullTrim($newLine);
        $res = matchStartEndFor($lines, $trimmedNewLine, 'function ', '(');
        if (!$res) {
            $res = matchStartEndFor($lines, $trimmedNewLine, 'public function ', '(');
            if (!$res) {
                $res = matchStartEndFor($lines, $trimmedNewLine, 'protected function ', '(');
                if (!$res) {
                    $res = matchStartEndFor($lines, $trimmedNewLine, 'private function ', '(');
                    if (!$res) {
                        $res = matchStartEndFor($lines, $trimmedNewLine, 'class ', ' ');
                    }
                }
            }
        }
        if ($res !== null) {
            $appendStart = $res['start'];
            $appendEnd = $res['end'];
            break;
        }
    }
    $newCode = mergeNewLines($lines, $newLines, $appendStart, $appendEnd);
    return $newCode;
}

