#!/usr/bin/env php
<?php
if (count($argv) == 2) {
	if ($argv[1] == "reactive-script-reacts-to") {
		# Write one event pr line that this script will react to
		echo "'.php' 'command' 'go-to-definition'\n";
		echo "'.yml' 'command' 'go-to-definition'\n";
		echo "'.yaml' 'command' 'go-to-definition'\n";
		echo "'user-selected' 'php-go-to-definition-select-use' *\n";
		echo "'user-selected' 'php-go-to-definition-select-window' *\n";
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

if ($argv[1] === "'.php' 'command' 'go-to-definition'") {
	$windows = getWindows();
	$handler = new DefinitionHandler();
	$match   = $handler->getMatch();
	if ($match !== null) {
		selectWindow($match['file'], $match['line'], $match['column'], $windows);
	}
} else if ($argv[1] === "'.yml' 'command' 'go-to-definition'" || $argv[1] === "'.yaml' 'command' 'go-to-definition'") {
	$windows = getWindows();
	$handler = new DefinitionHandler();
	$caret   = $handler->getCaret();
	$ns      = $handler->getWordUnderCaret($caret, $handler->getNSOperators());
	$match   = $handler->positionFromSignature($ns);
	if ($match !== null) {
		selectWindow($match['file'], $match['line'], $match['column'], $windows);
		return;
	}
	$service = $handler->getWordUnderCaret($caret, $handler->getDINameOperators());
	$match   = $handler->findInServices($caret, $service.':');
	if ($match !== null) {
		selectWindow($match['file'], $match['line'], $match['column'], $windows);
	}
} else if (strpos($argv[1], "'user-selected' 'php-go-to-definition-select-use'") === 0) {
	$windows   = getWindows();
	$selection = substr($argv[1], 51, strlen($argv[1])-52);
	if ($selection === "user-cancelled") {
		return;
	}

	$handler  = new DefinitionHandler();
	$position = $handler->positionFromSignature($selection);
	if ($position === null) {
		return;
	}

	selectWindow($position['file'], $position['line'], $position['column'], $windows);
} else if (strpos($argv[1], "'user-selected' 'php-go-to-definition-select-window'") === 0) {
	$selection = substr($argv[1], 54, strlen($argv[1])-55);
	if ($selection === "user-cancelled") {
		return;
	}

	gotoFile($selection);
}

function getWindows() {
	$windows = [];
	echo "request|editor get-windows\n";
	while (true) {
		$line = readline();
		if ($line === 'end-of-conversation') {
			break;
		}

		if ($line === '') {
			continue;
		}

		if (!in_array($line, $windows)) {
			array_push($windows, $line);
		}
	}

	return $windows;
}

function selectWindow($file, $line, $column, $windows) {
	$list        = "";
	$windowCount = 0;
	foreach ($windows as $window) {
		if ($list === '') {
			$separator = '';
		} else {

			$separator = ',';
		}

		$list = $list.$separator.$file.'|'.$line.'|'.$column.'|'.$window.'||'.$window;
		$windowCount++;
	}
	if ($windowCount > 1) {
		echo 'command|editor user-select php-go-to-definition-select-window "'.$list.'"'."\n";
	} else {
		gotoFile($file.'|'.$line.'|'.$column);
	}
}

function gotoFile($locationStr) {
	echo 'command|editor goto "'.$locationStr.'"'."\n";
}

class DefinitionHandler {
    private $caret;

    public function __construct() {
        $this->caret = $this->parseCaret();
    }

    public function getCaret() {
        return $this->caret;
    }

	public function getMatch() {
		$caret = $this->caret;
		// Find class definition
		$word = $this->getWordUnderCaret($caret, $this->getOperators());
		$uses = $this->getUses($caret);
		foreach ($uses as $use) {
			if ($this->endsWith($use['alias'], "\\".$word)) {
				return $this->positionFromSignature($use['name']);
			}
		}

		// Find full class namespace from string
		$ns = $this->getWordUnderCaret($caret, $this->getNSOperators());
		if ($ns !== $word) {
			$location = $this->positionFromSignature($ns);
			if ($location !== null) {
				return $location;
			}
		}

		// Find service definition
		$service  = $this->getWordUnderCaret($caret, $this->getDINameOperators());
		$location = $this->findInServices($caret, $service.':');
		if ($location !== null) {
			return $location;
		}

		// Find any class matching word
		return $this->chooseAllMatches($word);
	}

	public function findInServices($caret, $phrase) {
		exec('oi locate "services*.yml"', $output);
		if (count($caret) !== 0) {
			$closest = $this->getClosestFile($output, explode('|', $caret[0])[0]);
			if ($closest !== null) {
				$location = $this->matchInFile($closest, $phrase);
				if ($location !== null) {
					return $location;
				}
			}
		}

		foreach ($output as $file) {
			$location = $this->matchInFile($file, $phrase);
			if ($location !== null) {
				return $location;
			}
		}

		return null;
	}

	public function matchInFile($file, $phrase) {
		$lines  = file($file, FILE_IGNORE_NEW_LINES);
		$lineNr = 1;
		foreach ($lines as $line) {
			if ($phrase === trim($line, " \t")) {
				return [
					'file'   => realpath($file),
					'line'   => (string) ($lineNr+1),
					'column' => '0'
				];
			}
			$lineNr++;
		}
		return null;
	}

	public function getClosestFile($files, $templateFile) {
		$current   = null;
		$diffPoint = 0;
		foreach ($files as $file) {
			$fullpath = realpath($file);
			for ($i = 0; $i < strlen($templateFile); $i++) {
				if (strlen($fullpath) < $i+1) {
					break;
				}

				if ($fullpath[$i] === $templateFile[$i]) {
					if ($diffPoint < $i) {
						$current   = $fullpath;
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
		if (count($matches) === 0) {
			return null;
		}

		if (count($matches) === 1) {
			return $this->positionFromSignature($matches[0]);
		}

		$matchList = "";
		foreach ($matches as $match) {
			if ($matchList !== "") {
				$matchList = $matchList.',';
			}

			$matchList = $matchList.$match;
		}
		echo 'command|editor user-select "php-go-to-definition-select-use" "'.$matchList."\"\n";
		return null;
	}

	public function positionFromSignature($use) {
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

	public function getUses($caret) {
		$uses = [];
		for ($i = 1; $i < count($caret); $i++) {
			$line = trim($caret[$i], " \t");
			if (strpos($line, 'use ') === 0) {
				$use       = substr($line, 4, strlen($line)-5);
				$useChunks = explode(' ', $use);
				if (count($useChunks) === 1) {
					$uses[] = ['name' => $use, 'alias' => $use];
				} else {
					if (count($useChunks) === 3) {
						if (strtolower($useChunks[1]) === 'as') {
							$pathChunks = explode("\\", $useChunks[0]);
							$basepath   = substr($useChunks[0], 0, strlen($useChunks[0])-strlen($pathChunks[count($pathChunks)-1]));
							$uses[]     = ['name' => $useChunks[0], 'alias' => $basepath."\\".$useChunks[2]];
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
			$line = readline();
			if ($line === 'end-of-conversation') {
				break;
			}

			if (strpos($line, 'php|signature|') === 0) {
				array_push($lines, explode('|', $line)[3]);
			}
		}
		return $lines;
	}

	public function parseCaret() {
		echo "request|editor get-caret\n";
		$lines = [];
		while (true) {
			$line = readline();
			if ($line === 'end-of-conversation') {
				break;
			}

			array_push($lines, $line);
		}
		return $lines;
	}

	public function getWordUnderCaret($lines, $operators) {
		if (count($lines) > 0) {
			$position = explode('|', $lines[0]);
			if (count($position) >= 3) {
				$line   = intval($position[1]);
				$column = intval($position[2]);
				$start  = $this->getWordStart($lines[$line], $column, $operators);
				if ($start === -1) {
					return;
				}

				$end = $this->getwordEnd($lines[$line], $column, $operators);
				return trim(substr($lines[$line], $start, $end-$start));
			}
		}
		return;
	}

	public function getWordStart($line, $column, $operators) {
		$startAt = $column-1;
		// If we are at the start of the word jump one back
		if (in_array($line[$startAt], $operators, true)) {
			$startAt = $startAt-1;
		}

		for ($i = $startAt; $i >= 1; $i--) {
			if (in_array($line[$i-1], $operators, true)) {
				return $i;
			}
		}

		return -1;
	}

	public function getWordEnd($line, $column, $operators) {
		for ($i = $column; $i < strlen($line)+1; $i++) {
			if (in_array($line[$i-1], $operators, true)) {
				return $i-1;
			}
		}

		return strlen($line);
	}

	public function getDINameOperators() {
		return ['{', '}', '[', ']', '(', ')', ',', "'", '"', '+', '-', '/', '\\', '>', '<', '*', '^', '=', '!', '&', ':', ';', ' ', "\n", '@'];
	}

	public function getNSOperators() {
		return ['{', '}', '[', ']', '(', ')', '.', ',', "'", '"', '+', '-', '/', '>', '<', '*', '^', '=', '!', '&', ':', ';', ' ', "\n", '@'];
	}

	public function getOperators() {
		return ['{', '}', '[', ']', '(', ')', '.', ',', "'", '"', '+', '-', '/', '\\', '>', '<', '*', '^', '=', '!', '&', ':', ';', ' ', "\n", '@'];
	}

	function endsWith($haystack, $needle) {
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
}
