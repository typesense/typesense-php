<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class NLSearchModel
 *
 * @package \Typesense
 */
class NLSearchModel
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
     * NLSearchModel constructor.
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
     * Update an existing NL search model.
     *
     * @example
     * $client->nlSearchModels['model-1']->update(['model_name' => 'openai/gpt-4'])
     *
     * @see https://typesense.org/docs/latest/api/natural-language-search.html
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
     * Retrieve a specific NL search model by its ID.
     *
     * @example
     * $client->nlSearchModels['model-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/natural-language-search.html
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * Delete a specific NL search model by its ID.
     *
     * @example
     * $client->nlSearchModels['model-1']->delete()
     *
     * @see https://typesense.org/docs/latest/api/natural-language-search.html
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
        return sprintf('%s/%s', NLSearchModels::RESOURCE_PATH, encodeURIComponent($this->id));
    }
} 