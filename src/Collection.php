<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Collection
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collection
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var Documents
     */
    public Documents $documents;

    /**
     * @var Overrides
     */
    public Overrides $overrides;

    /**
     * @var Synonyms
     */
    public Synonyms $synonyms;

    /**
     * Collection constructor.
     *
     * @param string $name
     * @param ApiCall $apiCall
     */
    public function __construct(string $name, ApiCall $apiCall)
    {
        $this->name      = $name;
        $this->apiCall   = $apiCall;
        $this->documents = new Documents($name, $this->apiCall);
        $this->overrides = new Overrides($name, $this->apiCall);
        $this->synonyms  = new Synonyms($name, $this->apiCall);
    }

    /**
     * @return string
     */
    public function endPointPath(): string
    {
        return sprintf('%s/%s', Collections::RESOURCE_PATH, $this->name);
    }

    /**
     * @return Documents
     */
    public function getDocuments(): Documents
    {
        return $this->documents;
    }

    /**
     * @return Overrides
     */
    public function getOverrides(): Overrides
    {
        return $this->overrides;
    }

    /**
     * @return Synonyms
     */
    public function getSynonyms(): Synonyms
    {
        return $this->synonyms;
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }
}
