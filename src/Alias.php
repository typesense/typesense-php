<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

/**
 * Class Alias
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Alias
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
     * Alias constructor.
     *
     * @param  \Typesense\Lib\Configuration  $config
     * @param  string  $name
     */
    public function __construct(Configuration $config, string $name)
    {
        $this->config  = $config;
        $this->name    = $name;
        $this->apiCall = new ApiCall($this->config);
    }

    /**
     * @return string
     */
    public function endPointPath(): string
    {
        return sprintf('%s/%s', Aliases::RESOURCE_PATH, $this->name);
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