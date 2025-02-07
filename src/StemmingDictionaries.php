<?php

namespace Typesense;

class StemmingDictionaries implements \ArrayAccess
{
    const RESOURCE_PATH = '/stemming/dictionaries';

    private ApiCall $apiCall;
    private $typesenseDictionaries = [];

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    public function __get($id)
    {
        if (!isset($this->typesenseDictionaries[$id])) {
            $this->typesenseDictionaries[$id] = new StemmingDictionary($id, $this->apiCall);
        }
        return $this->typesenseDictionaries[$id];
    }

    public function upsert($id, $wordRootCombinations)
    {
        $dictionaryInJSONLFormat = is_array($wordRootCombinations) ? implode(
            "\n",
            array_map(
                static fn(array $wordRootCombo) => json_encode($wordRootCombo, JSON_THROW_ON_ERROR),
                $wordRootCombinations
            )
        ) : $wordRootCombinations;

        $resultsInJSONLFormat = $this->apiCall->post($this->endpoint_path("import"), $dictionaryInJSONLFormat, false, ["id" => $id]);

        return is_array($wordRootCombinations) ? array_map(
            static function ($item) {
                return json_decode($item, true, 512, JSON_THROW_ON_ERROR);
            },
            array_filter(
                explode("\n", $resultsInJSONLFormat),
                'strlen'
            )
        ) : $resultsInJSONLFormat;
    }

    public function retrieve()
    {
        $response = $this->apiCall->get(StemmingDictionaries::RESOURCE_PATH, []);

        // If response is null, return empty dictionaries structure
        if ($response === null) {
            return ['dictionaries' => []];
        }
        return $response;
    }

    private function endpoint_path($operation = null)
    {
        return $operation === null ? self::RESOURCE_PATH : self::RESOURCE_PATH . "/" . encodeURIComponent($operation);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->typesenseDictionaries[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): StemmingDictionary
    {
        if (!isset($this->typesenseDictionaries[$offset])) {
            $this->typesenseDictionaries[$offset] = new StemmingDictionary($offset, $this->apiCall);
        }

        return $this->typesenseDictionaries[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->typesenseDictionaries[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->typesenseDictionaries[$offset]);
    }
}
