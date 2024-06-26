<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Collections;
use Typesense\Stopwords;
use Typesense\Aliases;
use Typesense\Keys;
use Typesense\Debug;
use Typesense\Metrics;
use Typesense\Health;
use Typesense\Operations;
use Typesense\MultiSearch;
use Typesense\Presets;
use Typesense\Analytics;
use Typesense\Conversations;

class ClientTest extends TestCase
{
    public function testCanCreateAClient(): void
    {
        $this->assertInstanceOf(Collections::class,  $this->client()->collections);
        $this->assertInstanceOf(Stopwords::class,  $this->client()->stopwords);
        $this->assertInstanceOf(Aliases::class,  $this->client()->aliases);
        $this->assertInstanceOf(Keys::class,  $this->client()->keys);
        $this->assertInstanceOf(Debug::class,  $this->client()->debug);
        $this->assertInstanceOf(Metrics::class,  $this->client()->metrics);
        $this->assertInstanceOf(Health::class,  $this->client()->health);
        $this->assertInstanceOf(Operations::class,  $this->client()->operations);
        $this->assertInstanceOf(MultiSearch::class,  $this->client()->multiSearch);
        $this->assertInstanceOf(Presets::class,  $this->client()->presets);
        $this->assertInstanceOf(Analytics::class,  $this->client()->analytics);
        $this->assertInstanceOf(Conversations::class,  $this->client()->conversations);
    }
}
