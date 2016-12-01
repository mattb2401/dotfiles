#!/usr/bin/env php
<?php
if (count($argv) == 2) {
	if ($argv[1] == "reactive-script-reacts-to") {
		echo "'tamper-at-caret' 'melin-cqrs-event-from-caret'\n";
		echo "'tamper-at-caret' 'melin-cqrs-handle-command-from-caret'\n";
		echo "'tamper-at-caret' 'melin-cqrs-apply-event-from-caret'\n";
		echo "'tamper-at-caret' 'melin-cqrs-new-command-from-caret'\n";
		echo "'tamper-at-caret' 'melin-cqrs-new-command-api-from-caret'\n";
		echo "'navigate-at-caret' 'melin-cqrs-got-commandhandler-from-caret'\n";
		echo "'navigate-at-caret' 'melin-cqrs-got-aggregateroot-from-caret'\n";
		echo "'navigate-at-caret' 'melin-cqrs-got-eventhandler-from-caret'\n";
		echo "'navigate-at-caret' 'melin-cqrs-got-test-from-caret'\n";
		echo "'user-inputted' 'melin-cqrs-new-command-from-caret-input' *\n";
		echo "'user-selected' 'melin-cqrs-select-window' *\n";
		exit();
	}
}

# Write scirpt code here.
#   Param 1: event
#   Param 2: global profile name
#   Param 3: local profile name
#
# When calling other commandsc use the --profile=PROFILE_NAME and
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.

$backendPrefix = '';
if (is_dir('backend')) {
    $backendPrefix = 'backend'.DIRECTORY_SEPARATOR;
}

if ($argv[1] === "'tamper-at-caret' 'melin-cqrs-event-from-caret'") {
	$caret    = new Caret();
	$word     = $caret->wordAtCaret();
	$document = new Document($caret->lines);
	$file     = $backendPrefix.'src'.DIRECTORY_SEPARATOR.$document->bundle.DIRECTORY_SEPARATOR.$document->aggregate.DIRECTORY_SEPARATOR.'Events'.DIRECTORY_SEPARATOR.$word.".php";
    
	$eventNS  = $document->bundle."\\".$document->aggregate."\\Events\\".$word;
	echo "event|'user-selected' 'php-add-use-statement' '".$eventNS."'\n";
	exec("oi generate event \"".$file."\"");
} else if ($argv[1] === "'tamper-at-caret' 'melin-cqrs-handle-command-from-caret'") {
	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->commandhandler === null) {
		return;
	}

	$handler = new CommandHandler($structure->commandhandler);
	$handler->generateHandler($caret->wordAtCaret());
} else if ($argv[1] === "'tamper-at-caret' 'melin-cqrs-apply-event-from-caret'") {
	$caret = new Caret();
	$root  = new AggregateRoot($caret);
	$root->applyEventUnderCaret();
} else if ($argv[1] === "'tamper-at-caret' 'melin-cqrs-new-command-from-caret'") {
	echo 'command|editor user-input "melin-cqrs-new-command-from-caret-input"'."\n";
} else if (strpos($argv[1], "'user-inputted' 'melin-cqrs-new-command-from-caret-input' '") === 0) {
	$value = getLastItem($argv[1], "'user-inputted' 'melin-cqrs-new-command-from-caret-input' '");
	if ($value === "user-cancelled") {
		return;
	}

	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->commandspath === null) {
		return;
	}

	$command = $backendPrefix.'src'.DIRECTORY_SEPARATOR.$document->bundle.DIRECTORY_SEPARATOR.$document->aggregate.DIRECTORY_SEPARATOR.'Commands'.DIRECTORY_SEPARATOR.$value.".php";
	exec("oi generate command \"".$command."\"");
} else if ($argv[1] === "'tamper-at-caret' 'melin-cqrs-new-command-api-from-caret'") {
	$caret       = new Caret();
	$document    = new Document($caret->lines);
	$apifile     = $backendPrefix.'src'.DIRECTORY_SEPARATOR.$document->bundle.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.$document->aggregate."Controller.php";
	$apitestfile = $backendPrefix.'src'.DIRECTORY_SEPARATOR.$document->bundle.DIRECTORY_SEPARATOR.'Tests'.DIRECTORY_SEPARATOR.'Unit'.DIRECTORY_SEPARATOR.$document->aggregate.DIRECTORY_SEPARATOR.$document->aggregate."CommandAPITest.php";
	exec("oi generate command-api \"".$apifile."\"");
	exec("oi generate command-api-test \"".$apitestfile."\"");
} else if ($argv[1] === "'navigate-at-caret' 'melin-cqrs-got-commandhandler-from-caret'") {
	$windows   = getWindows();
	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->commandhandler === null) {
		return;
	}
	selectWindow($structure->commandhandler, "1", "1", $windows);
} else if ($argv[1] === "'navigate-at-caret' 'melin-cqrs-got-aggregateroot-from-caret'") {
	$windows   = getWindows();
	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->aggregateRoot === null) {
		return;
	}

	selectWindow($structure->aggregateRoot, "1", "1", $windows);
} else if ($argv[1] === "'navigate-at-caret' 'melin-cqrs-got-eventhandler-from-caret'") {
	$windows   = getWindows();
	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->eventHandler === null) {
		return;
	}

	selectWindow($structure->eventHandler, "1", "1", $windows);
} else if ($argv[1] === "'navigate-at-caret' 'melin-cqrs-got-test-from-caret'") {
	$windows   = getWindows();
	$caret     = new Caret();
	$document  = new Document($caret->lines);
	$structure = new Structure($caret, $document);
	if ($structure->test === null) {
		return;
	}

	selectWindow($structure->test, "1", "1", $windows);
} else if (strpos($argv[1], "'user-selected' 'melin-cqrs-select-window'") === 0) {
	$selection = substr($argv[1], 44, strlen($argv[1])-45);
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
		echo 'command|editor user-select melin-cqrs-select-window "'.$list.'"'."\n";
	} else {
		gotoFile($file.'|'.$line.'|'.$column);
	}
}

function gotoFile($locationStr) {
	echo 'command|editor goto "'.$locationStr.'"'."\n";
}

function getLastItem($event, $match) {
	return substr($event, strlen($match), strlen($event)-strlen($match)-1);
}

class AggregateRoot {
	private $caret;

	public function __construct($caret) {
		$this->caret = $caret;
	}

	public function applyEventUnderCaret() {
		$document  = new Document($this->caret->lines);
		$methods   = $document->getMethods();
		$lastApply = null;
		foreach ($methods as $method) {
			if ($method->scope === 'public' || $method->scope === 'protected') {
				if (strpos($method->name, 'apply') === 0) {
					$lastApply = $method;
				}
			}
		}
		if ($lastApply === null) {
			$lastApply = $methods[count($methods)-1];
		}

		$name       = $this->caret->wordAtCaret();
		$namelength = strlen($name);
		$method     = '';
		$method     = $method."\n";
		$method     = $method.'    protected function apply'.substr($name, 0, $namelength-5).'('.$name.' $event)'."\n";
		$method     = $method.'    {'."\n";
		$method     = $method.'    }'."\n";
		$method     = $method."\n";
		$tempFile   = $this->writeTempFile($method);
		echo "command|editor insert \"".$tempFile."\" \"".$this->caret->file."|".(string) ($lastApply->end+2)."|1\"\n";
		echo "command|editor goto \"".$this->caret->file."|".(string) ($lastApply->end+4)."|6\"\n";
	}

	private function writeTempFile($text) {
		$tempfile = tempnam(sys_get_temp_dir(), '');
		$fp       = fopen($tempfile, 'w');
		fwrite($fp, $text);
		fclose($fp);
		return $tempfile;
	}
}

class CommandHandler {
	private $file;
	private $document;
	private $nextHandlerLine = 0;

	public function __construct($file) {
		$this->file            = $file;
		$contentsReader        = new FileContents($this->file);
		$contents              = $contentsReader->body;
		$this->document        = new Document($contents);
		$this->nextHandlerLine = $this->getNextHandlerLine();
	}

	public function generateHandler($name) {
		if ($this->nextHandlerLine === 0) {
			return;
		}

		$method   = '';
		$method   = $method."\n";
		$method   = $method.'    public function '.lcfirst($name).'('.$name.' $command)'."\n";
		$method   = $method.'    {'."\n";
		$method   = $method.'    }'."\n";
		$method   = $method."\n";
		$tempFile = $this->writeTempFile($method);
		echo "command|editor insert \"".$tempFile."\" \"".$this->file."|".(string) ($this->nextHandlerLine+1)."|1\"\n";
		echo "command|editor goto \"".$this->file."|".(string) ($this->nextHandlerLine+1)."|1\"\n";
	}

	private function getNextHandlerLine() {
		$methods          = $this->document->getMethods();
		$lastPublicMethod = null;
		foreach ($methods as $method) {
			if ($method->scope === 'public') {
				$lastPublicMethod = $method;
			}
		}

		if ($lastPublicMethod === null) {
			return null;
		}

		return $lastPublicMethod->end+1;
	}

	private function writeTempFile($text) {
		$tempfile = tempnam(sys_get_temp_dir(), '');
		$fp       = fopen($tempfile, 'w');
		fwrite($fp, $text);
		fclose($fp);
		return $tempfile;
	}
}

class FileContents {
	public $body = [];

	public function __construct($file) {
		$dirtyContents = $this->getDirtyContents($file);
		if ($dirtyContents !== null) {
			$this->body = $dirtyContents;
			return;
		}
		echo $file."\n";
		$this->body = file($file, FILE_IGNORE_NEW_LINES);
	}

	private function getDirtyContents($file) {
		echo "request|editor get-dirty-files \"".$file."\"\n";
		$response = [];
		while (true) {
			$line = readln();
			if ($line === 'end-of-conversation') {
				break;
			}

			if (trim($line) === '') {
				continue;
			}

			$response[] = $line;
		}
		if (count($response) < 1) {
			return null;
		}

		$dirtyfile = explode('|', $response[0])[1];
		$contents  = file($dirtyfile, FILE_IGNORE_NEW_LINES);
		unlink($dirtyfile);
		return $contents;
	}
}

class Structure {
	private $caret;
	private $document;

	public $classname;
	public $bundlepath;
	public $commandhandler;
	public $aggregateRoot;
	public $commandspath;
	public $eventHandler;
	public $readModelData;
	public $test;

	public function __construct($caret, $document) {
		$this->caret          = $caret;
		$this->document       = $document;
		$this->classname      = $this->getClassName();
		$this->bundlepath     = $this->getBundlePath();
		$this->commandhandler = $this->getCommandHandler();
		$this->commandspath   = $this->getCommandsPath();
		$this->aggregateRoot  = $this->getAggregate();
		$this->eventHandler   = $this->getEventHandler();
		$this->readModelData  = $this->getReadModelData();
		$this->test           = $this->getTest();
	}

	private function getClassName() {
		$path_parts = pathinfo($this->caret->file);
		return $path_parts['filename'];
	}

	private function getBundlePath() {
		$start = strpos($this->caret->file, $this->document->bundle);
		if ($start === false) {
			return null;
		}

		return substr($this->caret->file, 0, $start+strlen($this->document->bundle));
	}

	private function getCommandHandler() {
		if ($this->bundlepath == null) {
			return null;
		}

		return $this->bundlepath.DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR.$this->document->aggregate."CommandHandler.php";
	}

	private function getAggregate() {
		if ($this->bundlepath == null) {
			return null;
		}

		return $this->bundlepath.DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR.$this->document->aggregate.".php";
	}

	private function getCommandsPath() {
		if ($this->bundlepath == null) {
			return null;
		}

		return $this->bundlepath.DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR."Commands";
	}

	private function getEventHandler() {
		if ($this->bundlepath == null) {
			return null;
		}

		$basedir = $this->bundlepath.DIRECTORY_SEPARATOR.$this->document->aggregate;
		if (!file_exists($basedir)) {
			return null;
		}
		$inRoot = $basedir.DIRECTORY_SEPARATOR.$this->document->aggregate."EventHandler.php";
		if (file_exists($inRoot)) {
			return $inRoot;
		}
		$handlerDir = $basedir.DIRECTORY_SEPARATOR."EventHandlers";
		if (!file_exists($handlerDir)) {
			$handlerDir = $basedir.DIRECTORY_SEPARATOR."Handlers";
		}
		if (!file_exists($handlerDir)) {
			return null;
		}
		$file = $handlerDir.DIRECTORY_SEPARATOR.$this->document->aggregate."EventHandler.php";
		if (!file_exists($file)) {
			$file = $handlerDir.DIRECTORY_SEPARATOR.$this->document->aggregate."CollectionEventHandler.php";
		}
		if (!file_exists($file)) {
			return null;
		}
		return $file;
	}

	private function getReadModelData() {
		$token = "src".DIRECTORY_SEPARATOR.$this->document->bundle;
		$start = strpos($this->caret->file, $token);
		if ($start === false) {
			return null;
		}

		$name = str_replace("Bundle", "", $this->document->bundle);
		return substr($this->caret->file, 0, $start)."app".DIRECTORY_SEPARATOR."DataFixtures".DIRECTORY_SEPARATOR."Test".DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR."LoadReadModelData.php";
	}

	private function getTest() {
		$token = $this->bundlepath.DIRECTORY_SEPARATOR."Tests".DIRECTORY_SEPARATOR."Unit".DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR.$this->classname."Test.php";
		if (!file_exists($token)) {
			$token = $this->bundlepath.DIRECTORY_SEPARATOR."Tests".DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR."Unit".DIRECTORY_SEPARATOR.$this->classname."Test.php";
			if (!file_exists($token)) {
			    $token = $this->bundlepath.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."Unit".DIRECTORY_SEPARATOR.$this->document->aggregate.DIRECTORY_SEPARATOR.$this->classname."Test.php";
			    if (!file_exists($token)) {
				    return null;
                }
			}
		}
		return $token;

	}
}

class Method {
	public $scope;
	public $name;
	public $start;
	public $end;
}

class Document {
	private $lines;

	public $namespace;
	public $bundle;
	public $module;
	public $aggregate;

	public function __construct($lines) {
		$this->lines     = $lines;
		$this->namespace = $this->getNamespace();
		$this->bundle    = $this->getBundle();
		$this->module    = $this->getModule();
		$this->aggregate = $this->getAggregate();
	}

	public function getMethods() {
		$methods  = [];
		$lineNr   = -1;
		$startPos = 0;
		$brackets = -1;
		$scope    = null;
		$name     = '';
		foreach ($this->lines as $line) {
			$lineNr++;
			$trimmed = trim($line);
			if ($scope === null) {
				if (strpos($trimmed, 'public function ') === 0) {
					$scope = 'public';
				} else if (strpos($trimmed, 'private function ') === 0) {
					$scope = 'private';
				} else if (strpos($trimmed, 'protected function ') === 0) {
					$scope = 'protected';
				}

				if ($scope === null) {
					continue;
				}

				$prefix   = strlen($scope.' function ');
				$name     = substr($trimmed, $prefix, strpos($trimmed, '(', $prefix)-$prefix);
				$startPos = $lineNr;
			}
			foreach (str_split($trimmed) as $char) {
				if ($char === '{') {
					if ($brackets === -1) {
						$brackets = 1;
					} else {

						$brackets++;
					}

					continue;
				}
				if ($char === '}') {
					$brackets--;
				}

				if ($brackets === 0) {
					$method        = new Method();
					$method->scope = $scope;
					$method->name  = $name;
					$method->start = $startPos;
					$method->end   = $lineNr;
					$methods[]     = $method;
					$brackets      = -1;
					$scope         = null;
					$name          = '';
				}
			}
		}
		return $methods;
	}

	private function getNamespace() {
		foreach ($this->lines as $line) {
			$trimmed = trim($line);
			$start   = strpos($trimmed, 'namespace ');
			if ($start === 0) {
				return str_replace(';', '', substr($trimmed, strlen('namespace ')));
			}
		}

		return null;
	}

	private function getBundle() {
		$chunks = explode("\\", $this->namespace);
		if (count($chunks) < 2) {
			return null;
		}
		return $chunks[0];
	}

	private function getModule() {
		if (strpos($this->bundle, 'Platform') === 0) {
			$end = strpos($this->bundle, 'Bundle');
			if ($end !== false) {
				return substr($this->bundle, strlen('Platform'), strlen($this->bundle)-$end-2);
			}
		}
		if (strpos($this->bundle, 'Module') === 0) {
			$end = strpos($this->bundle, 'Bundle');
			if ($end !== false) {
				return substr($this->bundle, strlen('Module'), strlen($this->bundle)-$end-2);
			}
		}
		if ($this->bundle === 'AppBundle') {
			return "App";
		}
		return null;
	}

	private function getAggregate() {
		$chunks = explode("\\", $this->namespace);
		if (count($chunks) < 2) {
			return null;
		}
		if ($chunks[1] === 'Tests' || $chunks[0] === 'Tests') {
			return $chunks[2];
		} else {
			return $chunks[1];
		}
	}
}

class Caret {
	public $file   = '';
	public $line   = 1;
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

		$chunks       = explode('|', $rawCaret[0]);
		$this->file   = $chunks[0];
		$this->line   = intval($chunks[1]);
		$this->column = intval($chunks[2]);
		for ($i = 1; $i < count($rawCaret); $i++) {
			$this->lines[] = $rawCaret[$i];
		}
	}

	public function wordAtCaret() {
		$line  = $this->lines[$this->line-1];
		$start = $this->getWordStart($line, $this->column);
		if ($start === -1) {
			return;
		}

		$end = $this->getwordEnd($line, $this->column);
		return trim(substr($line, $start, $end-$start));
	}

	private function getWordStart($line, $column) {
		$operators = $this->getOperators();
		$startAt   = $column-1;
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

	private function getWordEnd($line, $column) {
		$operators = $this->getOperators();
		for ($i = $column; $i < strlen($line)+1; $i++) {
			if (in_array($line[$i-1], $operators, true)) {
				return $i-1;
			}
		}

		return strlen($line);
	}

	private function getOperators() {
		return ['{', '}', '[', ']', '(', ')', '.', ',', "'", '"', '+', '-', '/', '\\', '>', '<', '*', '^', '=', '!', '&', ':', ';', ' ', "\n", '@'];
	}
}

function readln() {
	return trim(fgets(STDIN), PHP_EOL);
}
