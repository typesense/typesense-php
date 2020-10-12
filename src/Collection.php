<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

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
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var \Typesense\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var \Typesense\Documents
     */
    public Documents $documents;

    /**
     * @var \Typesense\Overrides
     */
    public Overrides $overrides;

    /**
     * Collection constructor.
     *
     * @param $config
     * @param $name
     */
    public function __construct(Configuration $config, string $name)
    {
        $this->config    = $config;
        $this->name      = $name;
        $this->apiCall   = new ApiCall($config);
        $this->documents = new Documents($config, $name);
        $this->overrides = new Overrides($config, $name);
    }

    /**
     * @return string
     */
    public function endPointPath(): string
    {
        return sprintf('%s/%s', Collections::RESOURCE_PATH, $this->name);
    }

    /**
     * @return \Typesense\Documents
     */
    public function getDocuments(): Documents
    {
        return $this->documents;
    }

    /**
     * @return \Typesense\Overrides
     */
    public function getOverrides(): Overrides
    {
        return $this->overrides;
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

}