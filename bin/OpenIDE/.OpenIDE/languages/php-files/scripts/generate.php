#!/usr/bin/env php

<?php
# Script parameters
#   Param 1: Script run location
#   Param 2: global profile name
#   Param 3: local profile name
#   Param 4-: Any passed argument
#
# When calling oi use the --profile=PROFILE_NAME and
# --global-profile=PROFILE_NAME argument to ensure calling scripts
# with the right profile.
#
# To post back oi commands print command prefixed by command| to standard output
# To post a comment print to std output prefixed by comment|
# To post an error print to std output prefixed by error|

if (count($argv) == 3) {
    if ($argv[2] == "get-command-definitions") {
        # Definition format usually represented as a single line:

        # Script description|
        # command1|"Command1 description"
        #   param|"Param description" end
        # end
        # command2|"Command2 description"
        #   param|"Param description" end
        # end

        echo "Generates a new class or domain concept|\n";
        echo "command|\"Creates a CQRS command\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        echo "value|\"Creates a CQRS value object\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        echo "command-test|\"Creates test fixture for a CQRS command\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        echo "event|\"Creates a CQRS event\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        echo "aggregate|\"Creates a CQRS aggregate, commandhandler and registers it\" \n";
        echo "  DIRECTORY|\"Path to folder holding the aggregate\" end \n";
        echo "end \n";
        echo "command-api|\"Creates a RestCqrsController\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        echo "command-api-test|\"Creates a command api test file\" \n";
        echo "  FILE|\"Path to file\" end \n";
        echo "end \n";
        exit();
    }
}

$psFramework = exec('oi conf read melin.framework');
if ($psFramework === '') {
    $psFramework = 'pslib';
}

if ($argc == 6) {
    $type = $argv[4];
    $filechunk = $argv[5];
    $file = $argv[1] . DIRECTORY_SEPARATOR . $filechunk;
    if ($type === 'aggregate') {
        createAggregate($file);
    } else {
        if (!endsWith($file, ".php")) {
            $file = $file . ".php";
        }
        runCommand($type, $file);
    }
}

function createAggregate($dir) {
    $name = basename($dir);
    $aggregateFile = $dir.DIRECTORY_SEPARATOR.$name.'.php';
    if (!file_exists($aggregateFile)) {
        runCommand('aggregate', $aggregateFile);
    }
    $commandHandlerFile = $dir.DIRECTORY_SEPARATOR.$name.'CommandHandler.php';
    $commandHandlerSignture = getNamespace(dirname($commandHandlerFile))."\\".basename($commandHandlerFile, ".php");
    if (!file_exists($commandHandlerFile)) {
        runCommand('command-handler', $commandHandlerFile);
    }

    $serviceFile = dirname($dir).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."services_app.yml";
    if (!file_exists($serviceFile)) {
        $serviceFile = dirname($dir).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."services.yml";
    }
    if (file_exists($serviceFile)) {
        $tempfile = tempnam(sys_get_temp_dir(), '');
        $fp = fopen($tempfile, 'w');
        appendToServiceDefinition($commandHandlerSignture, function ($line) use ($fp) {
            fwrite($fp, $line);
        });
        fclose($fp);
        $lines = getFileLines($serviceFile);
        echo "command|editor goto \"" . $serviceFile . "|1|1\"\n";
        sleep(1);
        echo "command|editor insert \"".$tempfile."\" \"".$serviceFile."|".(string)count($lines)."|".(string)(strlen($lines[count($lines)-1])+1)."\"\n";
        waitForEndOfCommand();
    }
}

function runCommand($type, $file) {
    $filename = basename($file, ".php");
    $directory = dirname($file);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
    $line = 0;
    if (!file_exists($file)) {
        $fp = fopen($file, 'w');
        $line = writeFile($type, $file, $filename, function ($line) use ($fp) {
            fwrite($fp, $line);
        });
        fclose($fp);
    }
    echo "command|editor goto \"" . $file . "|" . $line . "|100\"\n";
    waitForEndOfCommand();
}

function writeFile($type, $file, $filename, $writer) {
    global $psFramework;
    $line = 2;
    $namespace = getNamespace(dirname($file));
    $module = '';
    $writer("<?php\n");
    if ($namespace !== null) {
        $module = explode("\\", $namespace)[1];
        $writer("namespace " . $namespace . ";\n");
        if ($psFramework === 'pslib')
            $module = explode("\\", $namespace)[0];
        $line = 3;
    }
    $writer("\n");
    if ($type == "command") {
        $line = writeCommand($writer, $filename, $line);
        $testFile = getTestFileFromCommand($file, $filename);
        runCommand('command-test', $testFile);
    }
    if ($type == "value") {
        $line = writeValueObject($writer, $filename, $line);
    }
    if ($type == "command-test") {
        $line = writeCommandTest($writer, $filename, $line, $namespace);
    }
    if ($type == "event") {
        $line = writeEvent($writer, $filename, $line);
    }
    if ($type === "aggregate") {
        $line = writeAggregate($writer, $filename, $line);
    }
    if ($type === "command-handler") {
        $line = writeCommandHandler($writer, $filename, $module, $line);
    }
    if ($type === "command-api") {
        $line = writeCommandAPI($writer, $filename, $module, $line, $namespace);
    }
    if ($type === "command-api-test") {
        $line = writeCommandAPITest($writer, $filename, $module, $line, $namespace);
    }
    return $line;
}

function writeCommand($writer, $name, $line) {
    global $psFramework;
    if ($psFramework === 'portal') {
        $writer("use Melin\Framework\CQRS\Command;\n");
    } else {
        $writer("use PSLib\CQRS\Command;\n");
        $writer("use PSLib\SpecificationObjects\Builder\SpecificationBuilder;\n");
    }
    $writer("\n");
    $writer("class ".$name." extends Command\n");
    $writer("{\n");
    $writer("    public function getSpecification()\n");
    $writer("    {\n");
    $writer("        return SpecificationBuilder::create()\n");
    $writer("            ->build();\n");
    $writer("    }\n");
    $writer("}\n");
    $writer("\n");
    return $line + 7;
}

function writeValueObject($writer, $name, $line) {
    global $psFramework;
    if ($psFramework === 'portal') {
        $writer("use Melin\Framework\CQRS\ValueObject;\n");
    } else {
        $writer("use PSLib\SpecificationObjects\ValueObject;\n");
        $writer("use PSLib\SpecificationObjects\Builder\SpecificationBuilder;\n");
    }
    $writer("\n");
    $writer("class ".$name." extends ValueObject\n");
    $writer("{\n");
    $writer("    public function getSpecification()\n");
    $writer("    {\n");
    $writer("        return SpecificationBuilder::create()\n");
    $writer("            ->build();\n");
    $writer("    }\n");
    $writer("}\n");
    $writer("\n");
    return $line + 7;
}

function writeCommandTest($writer, $name, $line, $namespace) {
    global $psFramework;
    $nsChunks = explode("\\", $namespace);
    $command = '???';
    if (endsWith($name, 'Test'))
        $command = substr($name, 0, strlen($name) - 4);
    $document = new Document(['namespace '.$namespace.';']);
    if (!is_dir('backend')) {
        $writer("use AppBundle\\".$nsChunks[count($nsChunks)-1].'\Commands\\'.$command.';'.PHP_EOL);
        $writer("use Tests\AppCommandTestCase;".PHP_EOL);
    } else {
        $writer("use ".$nsChunks[0]."\\".$nsChunks[count($nsChunks)-1].'\Commands\\'.$command.';'.PHP_EOL);
        $writer("use Tests\AppCommandTestCase;".PHP_EOL);
    }
    $writer('use Ramsey\Uuid\Uuid;'.PHP_EOL);
    $writer(PHP_EOL);
    $writer('class '.$name.' extends '.$document->module.'AppCommandTestCase'.PHP_EOL);
    $writer('{'.PHP_EOL);
    $writer('}'.PHP_EOL);
    $writer(PHP_EOL);
    return $line + 1;
}

function writeEvent($writer, $name, $line) {
    global $psFramework;
    if ($psFramework === 'portal') {
        $writer("use Melin\Framework\CQRS\Event;\n");
    } else {
        $writer("use PSLib\CQRS\AbstractEvent;\n");
        $writer("use PSLib\SpecificationObjects\Builder\SpecificationBuilder;\n");
    }
    $writer("\n");
    $writer("class ".$name." extends AbstractEvent\n");
    $writer("{\n");
    $writer("    public function getSpecification()\n");
    $writer("    {\n");
    $writer("        return SpecificationBuilder::create()\n");
    $writer("            ->build();\n");
    $writer("    }\n");
    $writer("}\n");
    $writer("\n");
    return $line + 7;
}

function writeAggregate($writer, $name, $line) {
    global $psFramework;
    if ($psFramework === 'portal') {
        $writer("use Melin\Framework\CQRS\AggregateRoot;\n");
    } else {
        $writer("use PSLib\CQRS\AggregateRoot;\n");
    }
    $writer("use Ramsey\Uuid\Uuid;\n");
    $writer("\n");
    $writer("class ".$name." extends AggregateRoot\n");
    $writer("{\n");
    $writer('    public function __construct(Uuid $id)'."\n");
    $writer("    {\n");
    $writer('        parent::__construct($id);'."\n");
    $writer("    }\n");
    $writer("}\n");
    return $line + 4;
}

function writeCommandHandler($writer, $name, $module, $line) {
    global $psFramework;
    $aggregateName = str_replace("CommandHandler", "", $name);
    if ($psFramework === 'portal') {
        $writer("use Melin\Framework\CQRS\EventSourceRepository;\n");
        $writer("use Melin\\".$module."\\".$aggregateName."\\".$aggregateName.";\n");
    } else {
        $writer("use PSLib\CQRS\EventSourceRepository;\n");
        $writer("use PSLib\Auth\User;\n");
        $writer("use ".$module."\\".$aggregateName."\\".$aggregateName.";\n");
    }
    $writer("use Ramsey\Uuid\Uuid;\n");
    $writer("\n");
    $writer("class ".$name."\n");
    $writer("{\n");
    $writer('    private $repository;'."\n");
    $writer('    private $currentUser;'."\n");
    $writer("\n");
    $writer('    public function __construct(EventSourceRepository $repository, User $currentUser)'."\n");
    $writer("    {\n");
    $writer('        $this->repository = $repository;'."\n");
    $writer('        $this->currentUser = $currentUser;'."\n");
    $writer("    }\n");
    $writer("}\n");
    return $line + 12;
}

function appendToServiceDefinition($signature, $writer) {
    global $psFramework;
    $chunks = explode("\\", $signature);
    $name = $chunks[count($chunks)-2];
    $snakeName = camelCaseToSnakeCase($name);
    if ($psFramework === 'pslib')
        $moduleName = camelCaseToSnakeCase(str_replace("Bundle", "", $chunks[0]));
    else
        $moduleName = camelCaseToSnakeCase(str_replace("Bundle", "", $chunks[1]));
    $writer("\n");
    $writer("\n");
    $writer("    # CQRS - ".$name."\n");
    $writer("    ".$moduleName.".command_handler.".$snakeName.":\n");
    $writer("        class: ".$signature."\n");
    $writer("        arguments:\n");
    if ($psFramework === 'pslib' && is_dir('backend'))
        $writer("            - ".'"'."@".$moduleName.".cqrs.event.repository".'"'."\n");
    else
        $writer("            - ".'"'."@ps.cqrs.event.repository".'"'."\n");
    $writer("            - ".'"'."@=service('ps.context.authentication').getUser()".'"'."\n");
    $writer("        tags:\n");
    $writer("            - { name: app.command_handler }\n");
}

function writeCommandAPI($writer, $name, $module, $line, $namespace) {
    global $psFramework;
    $document = new Document(['namespace '.$namespace.';']);
    $moduleName = camelCaseToSnakeCase(str_replace("Bundle", "", $module));
    if ($psFramework === 'portal') {
        $writer('use Melin\Framework\Controller\RestCqrsController;'."\n");
        $writer('use Nelmio\ApiDocBundle\Annotation\ApiDoc;'."\n");
        $writer('use FOS\RestBundle\Controller\Annotations as Rest;'."\n");
        $writer('use Melin\Framework\CQRS\Validation\SpecificationRequestDeserializer;'."\n");
        $writer('use FOS\RestBundle\Util\Codes;'."\n");
        $writer('use Symfony\Component\HttpFoundation\Request;'."\n");
        $writer('use FOS\RestBundle\View\View;'."\n");
        $writer('use Ramsey\Uuid\Uuid;'."\n");
    } else {
        $writer('use Ramsey\Uuid\Uuid;'."\n");
        $writer('use Symfony\Component\HttpFoundation\Request;'."\n");
        $writer('use Symfony\Component\HttpFoundation\Response;'."\n");
        $writer('use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;'."\n");
        $writer('use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;'."\n");
        $writer('use Nelmio\ApiDocBundle\Annotation\ApiDoc;'."\n");
        $writer('use PSLib\Utils\Symfony\Controller\RestCqrsController;'."\n");
        $writer('use PSLib\Utils\Http\JsonResponse;'."\n");
        $writer('use PSLib\SpecificationObjects\SpecificationRequestDeserializer;'."\n");
    } 
    $writer("\n");
    $writer('/**'."\n");
    $writer(' * @Rest\NamePrefix("'.strtolower($document->module).'_")'."\n");
    $writer(' */'."\n");
    $writer('class '.$name.' extends RestCqrsController'."\n");
    $writer('{'."\n");
    $writer('    protected function getCommandDispatcher()'."\n");
    $writer('    {'."\n");
    $writer('        return $this->container->get('."'".$moduleName.'.cqrs.command_dispatcher'."'".');'."\n");
    $writer('    }'."\n");
    $writer("\n");
    $writer('    protected function getModuleQueryProvider()'."\n");
    $writer('    {'."\n");
    $writer('        return $this->container->get('."'".$moduleName.'.query.queries'."'".');'."\n");
    $writer('    }'."\n");
    $writer('}'."\n");
    return $line + 16;
}

function writeCommandAPITest($writer, $name, $module, $line, $namespace) {
    global $psFramework;
    $document = new Document(['namespace '.$namespace.';']);

    if ($psFramework === 'portal') {
        $writer('use Ramsey\Uuid\Uuid;'."\n");
        $writer('use FOS\RestBundle\Util\Codes;'."\n");
        $writer('use Melin\\'.$module.'\Tests\Unit\\'.$document->module.'CommandAPITestCase;'."\n");
    } else {
        $writer('use '.$module.'\Tests\\'.$document->module.'CommandTestCase;'."\n");
        $writer('use Ramsey\Uuid\Uuid;'."\n");
    } 
    $writer("\n");
    $writer('class '.$name.' extends '.$document->module.'CommandAPITestCase'."\n");
    $writer('{'."\n");
    $writer("    const API_URL = '/".strtolower($document->module)."/api/".strtolower(str_replace("CommandAPITest", "", $name))."s';\n");
    $writer('}'."\n");
    return $line + 5;
}

function getFileLines($file) {
    echo "request|editor get-dirty-files \"".$file."\"\n";
    $response = waitForEndOfConversation();
    if (count($response) == 0 || trim($response[0]) === "") {
        return file($file, FILE_IGNORE_NEW_LINES);
    }
    $tempFile = explode('|', $response[0])[1];
    $lines =  file($tempFile, FILE_IGNORE_NEW_LINES);
    unlink($tempFile);
    return $lines;
}

function getNamespace($runPath) {
    $namespace = "";
    $dir = exec("oi conf read php.namespace.basepath");
    if ($dir != "") {
        $dir = realpath($dir);
        if (!file_exists($dir) || strpos($runPath, $dir) !== 0)
            $dir = getcwd();
    } else {
        $dir = getcwd();
    }
    if (file_exists($dir))
        $dir = $dir . DIRECTORY_SEPARATOR;
    if (strlen($runPath) > strlen($dir)) {
        $namespace =
            substr(
                $runPath,
                strlen($dir),
                strlen($runPath) - strlen($dir));
        $namespace = str_replace('/', '\\', $namespace);
    }
    $basenamespace = exec("oi conf read php.namespace.base");
    if ($basenamespace != "") {
        if ($namespace != "")
            $namespace = $basenamespace . '\\' . $namespace;
        else
            $namespace = $basenamespace;
    }
    if ($namespace == "")
        return null;
    return ucfirst($namespace);
}

function waitForEndOfConversation() {
    $lines = [];
    while (TRUE) {
        $line = readln();
        if ($line == "end-of-conversation")
            break;
        $lines[] = $line;
    }
    return $lines;
}

function waitForEndOfCommand() {
    while (TRUE) {
        $line = readln();
        if ($line == "end-of-command")
            break;
    }
}

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function getTestFileFromCommand($commandFile, $filename) {
    global $psFramework;
    $path = "";
    $pathChunks = explode('/', $commandFile);
    if (is_dir('backend') || $psFramework !== 'pslib') {
        $extension = '/Tests/'.$pathChunks[count($pathChunks)-3].'/'.$filename.'Test.php';
        for ($i=0; $i < count($pathChunks)-3; $i++) {
            if ($pathChunks[$i] === '')
                continue;
            $path = $path.'/'.$pathChunks[$i];
        }
        return $path.$extension;
    }
    return getcwd().'/tests/Unit/'.$pathChunks[count($pathChunks)-3].'/'.$filename.'Test.php';
}

function camelCaseToSnakeCase($input) {
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}

function camelToSnake($val) {
    return
        preg_replace_callback(
            '/[A-Z]/',
            create_function('$match', 'return "_" . strtolower($match[0]);'),
            $val);
}

class Document
{
    private $lines;

    public $namespace;
    public $bundle;
    public $module;
    public $aggregate;

    public function __construct($lines) {
        $this->lines = $lines;
        $this->namespace = $this->getNamespace();
        $this->bundle = $this->getBundle();
        $this->module = $this->getModule();
        $this->aggregate = $this->getAggregate();
    }

    public function getMethods() {
        $methods = [];
        $lineNr = -1;
        $startPos = 0;
        $brackets = -1;
        $scope = null;
        $name = '';
        foreach ($this->lines as $line) {
            $lineNr++;
            $trimmed = trim($line);
            if ($scope === null) {
                if (strpos($trimmed, 'public function ') === 0)
                    $scope = 'public';
                else if (strpos($trimmed, 'private function ') === 0)
                    $scope = 'private';
                else if (strpos($trimmed, 'protected function ') === 0)
                    $scope = 'protected';
                if ($scope === null)
                    continue;
                $prefix = strlen($scope.' function ');
                $name = substr($trimmed, $prefix, strpos($trimmed, '(', $prefix) - $prefix);
                $startPos = $lineNr;
            }
            foreach (str_split($trimmed) as $char) {
                if ($char === '{') {
                    if ($brackets === -1)
                        $brackets = 1;
                    else
                        $brackets++;
                    continue;
                }
                if ($char === '}')
                    $brackets--;

                if ($brackets === 0) {
                    $method = new Method();
                    $method->scope = $scope;
                    $method->name = $name;
                    $method->start = $startPos;
                    $method->end = $lineNr;
                    $methods[] = $method;
                    $brackets = -1;
                    $scope = null;
                    $name = '';
                }
            }
        }
        return $methods;
    }

    private function getNamespace() {
        foreach ($this->lines as $line) {
            $trimmed = trim($line);
            $start = strpos($trimmed, 'namespace ');
            if ($start === 0)
                return str_replace(';', '', substr($trimmed, strlen('namespace ')));
        }
        return null;
    }

    private function getBundle() {
        global $psFramework;
        $chunks = explode("\\", $this->namespace);
        if ($psFramework === 'pslib') {
            if (count($chunks) < 2)
                return null;
            return $chunks[0];
        }
        if (count($chunks) < 3)
            return null;
        return $chunks[1];
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
        global $psFramework;
        $chunks = explode("\\", $this->namespace);
        if ($psFramework === 'pslib') {
            if (count($chunks) < 2)
                return null;
            if ($chunks[1] === 'Tests')
                return $chunks[2];
            else
                return $chunks[1];
        }
        if (count($chunks) < 3)
            return null;
        if ($chunks[2] === 'Tests')
            return $chunks[4];
        else
            return $chunks[2];
    }
}

function readln() {
    return trim(fgets(STDIN), PHP_EOL);
}
