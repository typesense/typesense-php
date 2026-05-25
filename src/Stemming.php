<?php

namespace Typesense;

class Stemming
{
    const RESOURCE_PATH = '/stemming';

    private ApiCall $apiCall;

    private StemmingDictionaries $typesenseDictionaries;


    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Access the stemming dictionaries resource. Use as a method-style endpoint to list or import
     * dictionaries, or chain `[$id]` on the returned object to access a single dictionary.
     *
     * @example
     * $client->stemming->dictionaries()->retrieve()
     * @example
     * $client->stemming->dictionaries()['en']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/stemming.html
     */
    public function dictionaries()
    {
        if (!isset($this->typesenseDictionaries)) {
            $this->typesenseDictionaries = new StemmingDictionaries($this->apiCall);
        }
        return $this->typesenseDictionaries;
    }
}
