<?php

namespace Feature;

use Tests\ConversationsTestCase;

class ConversationModelsTest extends ConversationsTestCase
{
    public const RESOURCE_PATH = '/conversations/models';

    public function testCanCreateAModel(): void
    {
        $data = [
            "model_name" => "openai/gpt-3.5-turbo",
            "api_key" => "OPENAI_API_KEY",
            "system_prompt" => "You are an assistant for question-answering...",
            "max_bytes" => 16384
        ];

        $this->mockApiCall()->allows()->post(static::RESOURCE_PATH, $data)->andReturns([]);

        $response = $this->mockConversations()->typesenseModels->create($data);
        $this->assertEquals([], $response);
    }

    public function testCanRetrieveAllModels(): void
    {
        $this->mockApiCall()->allows()->get(static::RESOURCE_PATH, [])->andReturns([]);
        $this->mockConversations()->typesenseModels->retrieve();

        $response = $this->client()->conversations->typesenseModels->retrieve();
        $this->assertEquals([], $response);
    }
}
