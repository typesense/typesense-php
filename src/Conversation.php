<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Conversation
 *
 * @package \Typesense
 */
class Conversation
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
     * Conversation constructor.
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
     * Update a conversation's TTL.
     *
     * @example
     * $client->conversations['conv-1']->update(['ttl' => 3600])
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
     * Retrieve a conversation by ID.
     *
     * @example
     * $client->conversations['conv-1']->retrieve()
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
     * Delete a conversation by ID.
     *
     * @example
     * $client->conversations['conv-1']->delete()
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
        return sprintf('%s/%s', Conversations::RESOURCE_PATH, encodeURIComponent($this->id));
    }
}
