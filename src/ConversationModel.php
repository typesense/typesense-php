<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class ConversationModel
 *
 * @package \Typesense
 */
class ConversationModel
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * ConversationModel constructor.
     *
     * @param string  $id
     * @param ApiCall $apiCall
     */
    public function __construct(string $id, ApiCall $apiCall)
    {
        $this->id = $id;
        $this->apiCall = $apiCall;
    }

    /**
     * Update a conversation model.
     *
     * @example
     * $client->conversations->typesenseModels['model-1']->update(['model_name' => 'openai/gpt-4', 'max_bytes' => 16384])
     *
     * @see https://typesense.org/docs/latest/api/conversational-search-rag.html
     *
     * @param array $params
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function update(array $params): array
    {
        return $this->apiCall->put($this->endPointPath(), $params);
    }

    /**
     * Retrieve a conversation model.
     *
     * @example
     * $client->conversations->typesenseModels['model-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/conversational-search-rag.html
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * Delete a conversation model.
     *
     * @example
     * $client->conversations->typesenseModels['model-1']->delete()
     *
     * @see https://typesense.org/docs/latest/api/conversational-search-rag.html
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

    /**
     * @return string
     */
    public function endPointPath(): string
    {
        return sprintf('%s/%s', ConversationModels::RESOURCE_PATH, encodeURIComponent($this->id));
    }
}
