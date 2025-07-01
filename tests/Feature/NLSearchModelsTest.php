<?php

namespace Feature;

use Tests\NLSearchModelsTestCase;

class NLSearchModelsTest extends NLSearchModelsTestCase
{
    public const RESOURCE_PATH = '/nl_search_models';

    public function testCanCreateAModel(): void
    {
        $data = [
            "id" => "test-collection-model",
            "model_name" => "openai/gpt-3.5-turbo",
            "api_key" => $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'),
            "system_prompt" => "You are a helpful search assistant.",
            "max_bytes" => 16384,
            "temperature" => 0.7
        ];

        $response = $this->client()->nlSearchModels->create($data);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('test-collection-model', $response['id']);
        $this->assertEquals('openai/gpt-3.5-turbo', $response['model_name']);
        
        $this->client()->nlSearchModels['test-collection-model']->delete();
    }

    public function testCanRetrieveAllModels(): void
    {
        $testData = [
            "id" => "retrieve-test-model",
            "model_name" => "openai/gpt-3.5-turbo",
            "api_key" => $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'),
            "system_prompt" => "Test model for retrieval.",
            "max_bytes" => 8192
        ];
        
        $this->client()->nlSearchModels->create($testData);
        
        $response = $this->client()->nlSearchModels->retrieve();
        $this->assertIsArray($response);
        
        $foundModel = false;
        foreach ($response as $model) {
            if ($model['id'] === 'retrieve-test-model') {
                $foundModel = true;
                $this->assertEquals('openai/gpt-3.5-turbo', $model['model_name']);
                break;
            }
        }
        $this->assertTrue($foundModel, 'Created test model should be found in the list');

        $this->client()->nlSearchModels['retrieve-test-model']->delete();
    }

    public function testCreateWithMissingRequiredFields(): void
    {
        $incompleteData = [
            "model_name" => "openai/gpt-3.5-turbo"
        ];

        $this->expectException(\Typesense\Exceptions\RequestMalformed::class);
        $this->client()->nlSearchModels->create($incompleteData);
    }

    public function testCreateWithInvalidModelName(): void
    {
        $invalidData = [
            "id" => "invalid-model-test",
            "model_name" => "invalid/model-name",
            "api_key" => $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'),
            "system_prompt" => "This should fail.",
            "max_bytes" => 16384
        ];

        $this->expectException(\Typesense\Exceptions\RequestMalformed::class);
        $this->client()->nlSearchModels->create($invalidData);
    }

    public function testUpdate(): void
    {
        $data = [
            "id" => "test-collection-model",
            "model_name" => "openai/gpt-3.5-turbo",
            "api_key" => $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'),
            "system_prompt" => "You are a helpful search assistant.",
            "max_bytes" => 16384,
            "temperature" => 0.7
        ];

        $response = $this->client()->nlSearchModels->create($data);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('test-collection-model', $response['id']);
        $this->assertEquals('openai/gpt-3.5-turbo', $response['model_name']);

        $response = $this->client()->nlSearchModels['test-collection-model']->update([
            "temperature" => 0.5
        ]);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('test-collection-model', $response['id']);
        $this->assertEquals(0.5, $response['temperature']);

        $this->client()->nlSearchModels['test-collection-model']->delete();
    }

    public function testDelete(): void
    {
        $data = [
            "id" => "test-collection-model",
            "model_name" => "openai/gpt-3.5-turbo",
            "api_key" => $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY'),
            "system_prompt" => "You are a helpful search assistant.",
            "max_bytes" => 16384,
            "temperature" => 0.7
        ];

        $response = $this->client()->nlSearchModels->create($data);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('test-collection-model', $response['id']);
        $this->assertEquals('openai/gpt-3.5-turbo', $response['model_name']);

        $response = $this->client()->nlSearchModels['test-collection-model']->delete();
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('test-collection-model', $response['id']);

    }
} 