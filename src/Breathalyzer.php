<?php

namespace ZxCoder\Breathalyzer;

class Breathalyzer
{
    /** @var DifferenceAnalyzer  */
    private $analyzer;

    /**
     * Breathalyzer constructor.
     * @param DifferenceAnalyzer $analyzer
     */
    public function __construct(DifferenceAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * @param $string
     * @return int
     */
    public function getDifference($string)
    {
        $string = preg_replace('/\s+/', ' ', strtolower(trim($string)));
        $words  = explode(' ', $string);

        $difference = 0;
        foreach ($this->getSameWords($words) as $word => $countSameWords) {
            $difference += $countSameWords * $this->analyzer->getDifference($word);
        }

        return $difference;
    }

    /**
     * @param array $words
     * @return array
     */
    private function getSameWords(array $words)
    {
        $sameWords = [];

        foreach ($words as $word) {
            if (!isset($sameWords[$word])) {
                $sameWords[$word] = 1;
            } else {
                $sameWords[$word]++;
            }
        }

        return $sameWords;
    }
}