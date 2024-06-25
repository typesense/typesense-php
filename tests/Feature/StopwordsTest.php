<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;


class StopwordsTest extends TestCase
{
    private $stopwordsUpsertRes = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stopwordsUpsertRes =  $this->client()->stopwords->put(
            [
                "name" => "stopword_set1",
                "stopwords" => ["Germany"],
                "locale" => "en"
            ]
        );
    }

    protected function tearDown(): void
    {
        $stopwords =  $this->client()->stopwords->getAll();
        foreach ($stopwords['stopwords'] as $stopword) {
            $this->client()->stopwords->delete($stopword['id']);
        }
    }

    public function testCanUpsertAStopword(): void
    {
        $this->assertEquals("stopword_set1", $this->stopwordsUpsertRes['id']);
        $this->assertEquals(["Germany"], $this->stopwordsUpsertRes['stopwords']);
        $this->assertEquals('en', $this->stopwordsUpsertRes['locale']);
    }

    public function testCanRetrieveAStopword(): void
    {
        $returnData = $this->client()->stopwords->get("stopword_set1");
        $this->assertEquals("stopword_set1", $returnData['stopwords']['id']);
    }

    public function testCanDeleteAStopword(): void
    {
        $returnData = $this->client()->stopwords->delete("stopword_set1");
        $this->assertEquals("stopword_set1", $returnData['id']);

        $this->expectException(ObjectNotFound::class);
        $this->client()->stopwords->get("stopword_set1");
    }

    public function testCanRetrieveAllStopwords(): void
    {
        $returnData = $this->client()->stopwords->getAll();
        $this->assertCount(1, $returnData['stopwords']);
    }
}
