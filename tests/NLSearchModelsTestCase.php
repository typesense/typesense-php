<?php

namespace Tests;

use Tests\TestCase;
use Typesense\NLSearchModels;

abstract class NLSearchModelsTestCase extends TestCase
{
    private NLSearchModels $mockNLSearchModels;

    protected function setUp(): void
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?? null;
        if (empty($apiKey)) {
            $this->markTestSkipped('OPENAI_API_KEY environment variable is not set. Skipping NL Search Models tests.');
            return;
        }

        parent::setUp();
        $this->mockNLSearchModels = new NLSearchModels(parent::mockApiCall());
    }

    protected function mockNLSearchModels(): NLSearchModels
    {
        return $this->mockNLSearchModels;
    }
} 