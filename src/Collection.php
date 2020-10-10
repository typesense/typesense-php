<?php

namespace Typesence;

use \Typesence\Lib\Configuration;

/**
 * Class Collection
 *
 * @package \Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collection
{

    /**
     * @var \Typesence\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var \Typesence\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * @var \Typesence\Documents
     */
    public Documents $documents;

    /**
     * @var \Typesence\Overrides
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
     * @return \Typesence\Documents
     */
    public function getDocuments(): Documents
    {
        return $this->documents;
    }

    /**
     * @return \Typesence\Overrides
     */
    public function getOverrides(): Overrides
    {
        return $this->overrides;
    }

    /**
     * @return array
     * @throws \Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws \Typesence\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

}