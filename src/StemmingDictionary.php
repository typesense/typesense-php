<?php

namespace Typesense;

class StemmingDictionary
{
    private $id;
    private ApiCall $apiCall;

    public function __construct(string $id, ApiCall $apiCall)
    {
        $this->id = $id;
        $this->apiCall  = $apiCall;
    }

    /**
     * Fetch details of a specific stemming dictionary.
     *
     * @example
     * $client->stemming->dictionaries()['en']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/stemming.html
     */
    public function retrieve()
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    private function endpointPath()
    {
        return StemmingDictionaries::RESOURCE_PATH . '/' . encodeURIComponent($this->id);
    }
}
