<?php

namespace ZxCoder\Breathalyzer\Test;

use PHPUnit\Framework\TestCase;
use ZxCoder\Breathalyzer\Breathalyzer;
use ZxCoder\Breathalyzer\DifferenceAnalyzer;

class TestBreathalyzer extends TestCase
{
    public function testBreathalyzer()
    {
        $dictionary   = file(__DIR__ . '/../vocabulary/vocabulary.txt');

        $analyzer     = new DifferenceAnalyzer($dictionary);
        $breathalyzer = new Breathalyzer($analyzer);

        $difference8   = $breathalyzer->getDifference(file_get_contents(__DIR__ . '/TestData/example_input'));
        $difference187 = $breathalyzer->getDifference(file_get_contents(__DIR__ . '/TestData/187'));

        self::assertEquals(8, $difference8);
        self::assertEquals(187, $difference187);
    }
}