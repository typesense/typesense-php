<?php

namespace Typesence;

use \Typesence\Lib\Configuration;

/**
 * Class Alias
 *
 * @package \Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Alias
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
     * Alias constructor.
     *
     * @param  \Typesence\Lib\Configuration  $config
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