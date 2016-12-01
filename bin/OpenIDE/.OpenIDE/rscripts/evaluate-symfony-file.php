#!/usr/bin/env php
<?php
if (count($argv) == 2) {
    if ($argv[1] == "reactive-script-reacts-to") {
        echo "'.php' 'command' 'evaluate-file'\n";
        exit(); 
    }
}

echo "request|editor get-caret\n";
$caret = [];
while (true) {
    $line = readline();
    if ($line === 'end-of-conversation')
        break;
    array_push($caret, $line);
}

$filepath = explode('|', $caret[0])[0]."\n";

if ($filepath === __FILE__) {
    return;
}

set_time_limit(0);

#if (file_exists('app/AppKernel.php')) {
#    require_once 'app/AppKernel.php';
#    $env = 'dev';
#    $debug = getenv('SYMFONY_DEBUG') !== '0' && $env !== 'prod';
#
#    $kernel = new AppKernel($env, $debug);
#    $kernel->boot();
#    $container = $kernel->getContainer();
#}

$content = '';
for ($i=1; $i < count($caret); $i++) {
    $trimmed = trim($caret[$i]);
    if (strpos($trimmed, '<?php') === 0)
        continue;
    if (strpos($trimmed, '?>') === 0)
        continue;
    $content = $content.$caret[$i]."\n";
}

echo "color|Yellow|evaluating...\n";
eval($content);
echo "\n";

