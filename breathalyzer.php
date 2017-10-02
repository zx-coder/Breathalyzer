<?php

include __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1]) || !is_file($argv[1])) {
    echo 'Basic usage: php breathalyzer.php path_to_input_file' . PHP_EOL;
    return;
}

$dictionary   = file(__DIR__ . '/vocabulary/vocabulary.txt');
$analyzer     = new \ZxCoder\Breathalyzer\DifferenceAnalyzer($dictionary);
$breathalyzer = new \ZxCoder\Breathalyzer\Breathalyzer($analyzer);

$difference   = $breathalyzer->getDifference(file_get_contents($argv[1]));
echo $difference;