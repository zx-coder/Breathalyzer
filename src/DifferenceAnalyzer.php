<?php

namespace ZxCoder\Breathalyzer;

class DifferenceAnalyzer
{
    const FIND_BEST_DIFFERENCE   = 1;
    const FIND_OK                = 0;

    /**
     * @var array
     */
    private $leveledDictionary = [];

    /**
     * DifferenceAnalyzer constructor.
     *
     * @param array $dictionary
     */
    public function __construct(array $dictionary)
    {
        $this->leveledDictionary = $this->createLeveledDictionary($dictionary);
    }

    /**
     * @param array $dictionary
     * @return array
     */
    private function createLeveledDictionary(array $dictionary)
    {
        $newDictionary = [];

        foreach ($dictionary as $word) {
            $correctedWord = strtolower(trim($word));
            $lenWord       = strlen($correctedWord);

            if ($lenWord > 255) {
                throw new \InvalidArgumentException(
                    sprintf('Cant score levenshtein difference with word from dictionary: %s', $word)
                );
            }

            $newDictionary[$lenWord][] = $correctedWord;
        }

        return $newDictionary;
    }

    /**
     * @throws \InvalidArgumentException
     * @param  string $word
     *
     * @return int
     */
    public function getDifference($word)
    {
        $word          = strtolower(trim($word));
        $lenWord       = strlen($word);
        $minDifference = $lenWord;

        if ($lenWord > 255) {
            throw new \InvalidArgumentException(
                sprintf('Cant score levenshtein difference with word: %s', $word)
            );
        }

        if ($this->isInDictionary($word)) {
            return 0;
        }

        $steps = [
            '+' => 0,
        ];

        for ($i = $lenWord; $i > 0; $i--) {
            foreach ($steps as $signStep => $step) {
                $index = $lenWord + ($signStep == '+' ? 1 : -1) * $step;
                if (!isset($this->leveledDictionary[$index])) {
                    continue;
                }

                $extraMinDifference =  ($steps['+'] == 0) ? 1 : $steps['+'];

                if ($this->scoreLevenshteinDifference(
                        $word,
                        $this->leveledDictionary[$index],
                        $minDifference,
                        $extraMinDifference
                    ) === self::FIND_BEST_DIFFERENCE) {
                    return $minDifference;
                }
            }

            if (!isset($steps['-'])) {
                $steps['-'] = 0;
            }

            $steps['-']++;
            $steps['+']++;
        }

        return $minDifference;
    }

    /**
     * @param  string $word
     * @return bool
     */
    private function isInDictionary($word)
    {
        return isset($this->leveledDictionary[strlen($word)]) &&
            in_array($word, $this->leveledDictionary[strlen($word)]);
    }

    /**
     * @param string  $word
     * @param array   $dictionary
     * @param integer $minDifference
     * @param integer $extraMinDifference
     *
     * @return integer
     */
    private function scoreLevenshteinDifference($word, array $dictionary, &$minDifference, $extraMinDifference)
    {
        foreach ($dictionary as $wordDict) {
            $difference = levenshtein($word, $wordDict);

            if ($minDifference > $difference) {
                $minDifference = $difference;
            }

            if ($minDifference == $extraMinDifference) {
                return self::FIND_BEST_DIFFERENCE;
            }
        }

        return self::FIND_OK;
    }
}