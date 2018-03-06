<?php

namespace webignition\Url\Query;

class Parser implements ParserInterface
{
    const PAIR_DELIMITER = '&';
    const KEY_VALUE_DELIMITER = '=';

    /**
     * Supplied query string, unmodified
     *
     * @var string
     */
    protected $origin = null;

    /**
     * @var array
     */
    protected $keyValuePairs = [];

    /**
     *
     * @param string $queryString
     */
    public function __construct($queryString)
    {
        $this->origin = $queryString;
        $this->parse();
    }

    /**
     * @return array
     */
    public function getKeyValuePairs()
    {
        return $this->keyValuePairs;
    }

    protected function parse()
    {
        if (empty($this->origin)) {
            return;
        }

        $pairStrings = explode(self::PAIR_DELIMITER, $this->origin);

        foreach ($pairStrings as $pairString) {
            $currentPair = explode(self::KEY_VALUE_DELIMITER, $pairString);
            $key = rawurldecode($currentPair[0]);
            $value = isset($currentPair[1]) ? rawurldecode($currentPair[1]) : null;

            $this->keyValuePairs[$key] = $value;
        }
    }
}
