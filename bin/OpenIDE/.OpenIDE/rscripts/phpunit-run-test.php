#!/usr/bin/env php
<?php
if (count($argv) == 2) {
	if ($argv[1] == "reactive-script-reacts-to") {
		# Write one event pr line that this script will react to
		echo "'evaluate-at-caret'\n";
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

$caret    = getCaret();
$location = getLocation($caret);
$filepath = str_replace(getcwd()."/", "", $location['file']);
if (endsWith($location['file'], '.php')) {
    $hasEvaluateTag = findEvaluateTag($location, $caret);
    if ($hasEvaluateTag)
        return;
	$test = getTestName($location, $caret);
    echo "\n";
	if ($test !== null) {
        printName($test);
	}
	$lines = [];
    $appPath = "";
    if (is_dir("backend"))
        $appPath = " -c backend/app";
    if (is_file("app/phpunit.xml"))
        $appPath = " -c app";
	$cmd   = "oi app phpunit ".$appPath." --filter \"/::".$test."*$/\" \"".$filepath."\"";
	if ($test === null) {
		echo "color|DarkYellow|".$filepath."\n";
		$cmd = "oi app phpunit ".$appPath." \"".$filepath."\"";
	}
	exec($cmd, $lines);
	foreach ($lines as $line) {
		if (strpos($line, "OK") !== false) {
			echo 'color|Green|'.$line."\n";
		} else if (strpos($line, " error:") !== false) {
			echo 'color|Red|'.$line."\n";
		} else if (strpos($line, " Errors:") !== false) {
			echo 'color|Red|'.$line."\n";
		} else if (strpos($line, "FAILURES!") !== false) {
			echo 'color|Red|'.$line."\n";
		} else {
			echo $line."\n";
		}
	}
}

function findEvaluateTag($location, $caret) {
	for ($i = $location['line']; $i > 0; $i--) {
		$match = strpos($caret[$i], '#!/oi/');
		if ($match !== FALSE) {
			return true;
		}
	}
	return false;
}

function getTestName($location, $caret) {
	for ($i = $location['line']; $i > 0; $i--) {
		$match = strpos($caret[$i], 'public function ');
		if ($match !== FALSE) {
			$match += 16;
			$end = strpos($caret[$i], '(');
			if ($match < $end) {
				return substr($caret[$i], $match, $end-$match);
			}
		}
	}
	return null;
}

function printName($line) {
    if (strpos($line, 'test') === 0) {
        $line = substr($line, 4, strlen($line) - 4);
    }
    $line = str_replace('_', ' ', $line);
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
    $lines = explode("\n", $line);
    foreach ($lines as $line) {
	    echo "color|DarkYellow|".$line."\n";
    }
}

function getCaret() {
	$lines = [];
	echo "request|editor get-caret\n";
	while (true) {
		$line    = readline();
		$lines[] = $line;
		if ($line === 'end-of-conversation') {
			break;
		}
	}
	return $lines;
}

function getLocation($caret) {
	$chunks = explode('|', $caret[0]);
	return [
		'file'   => $chunks[0],
		'line'   => intval($chunks[1]),
		'column' => intval($chunks[2])
	];
}

function endsWith($string, $pattern) {
	return strpos($string, $pattern) === (strlen($string)-strlen($pattern));
}
