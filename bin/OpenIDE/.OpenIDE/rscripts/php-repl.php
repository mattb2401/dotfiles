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

echo "server starting\n";
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
        foreach ($linesToEvaluate as $ln) {
            $code .= $ln."\n";
        }
        foreach ($selectionLines as $ln) {
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
            foreach ($selectionLines as $ln) {
                $linesToEvaluate[] = $ln;
            }
        } else {
            echo "error|".$err."\n";
        }
    }
}

