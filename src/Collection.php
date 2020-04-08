<?php

namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Collection
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collection
{

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Devloops\Typesence\ApiCall
     */
    private $apiCall;

    /**
     * @var \Devloops\Typesence\Documents
     */
    public $documents;

    /**
     * @var \Devloops\Typesence\Overrides
     */
    public $overrides;

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
     * @return \Devloops\Typesence\Documents
     */
    public function getDocuments(): Documents
    {
        return $this->documents;
    }

    /**
     * @return \Devloops\Typesence\Overrides
     */
    public function getOverrides(): Overrides
    {
        return $this->overrides;
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

}