<?php

namespace Feature;

use Tests\TestCase;

class StemmingDictionariesTest extends TestCase
{
    private $dictionaryId = 'test_dictionary';

    private $dictionary = [
            ["root" => "exampleRoot1", "word" => "exampleWord1"],
            ["root" => "exampleRoot2", "word" => "exampleWord2"]
    ];

    private $dictionaryUpsertResponse = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client()->stemming->dictionaries()->upsert(
            $this->dictionaryId, 
            $this->dictionary
        );
    }

    public function testCanUpsertADictionary(): void
    {
        $this->dictionaryUpsertResponse = $this->client()->stemming->dictionaries()->upsert($this->dictionaryId, $this->dictionary);
        $this->assertEquals($this->dictionary, $this->dictionaryUpsertResponse);
    }

    public function testCanRetrieveADictionary(): void
    {
        $returnData = $this->client()->stemming->dictionaries()[$this->dictionaryId]->retrieve();
        $this->assertEquals($returnData['id'], $this->dictionaryId);
    }


    public function testCanRetrieveAllDicitionaries(): void
    {
        $returnData = $this->client()->stemming->dictionaries()->retrieve();
        $this->assertCount(1, $returnData['dictionaries']);
        $this->assertEquals($returnData['dictionaries'][0], $this->dictionaryId);
    }
}
