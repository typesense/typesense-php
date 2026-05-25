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
     * Access the documents resource for this collection. Use as a method-style endpoint
     * to index, search, import or export documents, or as an array to access a single document.
     *
     * @example
     * $client->collections['products']->documents->create(['id' => '1', 'title' => 'Hat'])
     * @example
     * $client->collections['products']->documents['1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/documents.html
     *
     * @var Documents
     */
    public Documents $documents;

    /**
     * Access the legacy overrides (curation rules) for this collection. Use it to upsert
     * or list overrides, or as an array to access a single override by ID.
     *
     * @example
     * $client->collections['products']->overrides->upsert('promote-hat', ['rule' => ['query' => 'hat', 'match' => 'exact'], 'includes' => []])
     * @example
     * $client->collections['products']->overrides['promote-hat']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/curation.html
     *
     * @var Overrides
     */
    public Overrides $overrides;

    /**
     * Access the legacy synonyms for this collection. Use it to upsert or list synonyms,
     * or as an array to access a single synonym by ID.
     *
     * @example
     * $client->collections['products']->synonyms->upsert('syn-1', ['synonyms' => ['nyc', 'new york']])
     * @example
     * $client->collections['products']->synonyms['syn-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html
     *
     * @var Synonyms
     */
    public Synonyms $synonyms;

    /**
     * @var bool|null
     */
    private ?bool $exists = null;

    /**
     * Collection constructor.
     *
     * @param string  $name
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
        return sprintf('%s/%s', Collections::RESOURCE_PATH, encodeURIComponent($this->name));
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
     * Set collection exists flag.
     *
     * @param bool $exists
     *
     * @return void
     */
    public function setExists(bool $exists): void
    {
        $this->exists = $exists;
    }

    /**
     * @return bool|null
     */
    public function exists(): ?bool
    {
        if ($this->exists === null) {
            try {
                $this->retrieve();
                $this->exists = true;
            } catch (TypesenseClientError | HttpClientException $e) {
                $this->exists = false;
            }
        }

        return $this->exists;
    }

    /**
     * Retrieve the details of a collection, given its name.
     *
     * @example
     * $client->collections['products']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/collections.html#retrieve-a-collection
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * Update a collection's schema to modify the fields and their types.
     *
     * @example
     * $client->collections['products']->update(['fields' => [['name' => 'tags', 'type' => 'string[]']]])
     *
     * @see https://typesense.org/docs/latest/api/collections.html#update-or-alter-a-collection
     *
     * @param array $schema
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function update(array $schema): array
    {
        return $this->apiCall->patch($this->endPointPath(), $schema);
    }

    /**
     * Permanently drops a collection. This action cannot be undone. For large collections,
     * this might have an impact on read latencies.
     *
     * @example
     * $client->collections['products']->delete()
     *
     * @see https://typesense.org/docs/latest/api/collections.html#drop-a-collection
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }
}
