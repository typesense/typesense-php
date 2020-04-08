<?php


namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Alias
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Alias
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
     * Alias constructor.
     *
     * @param   \Devloops\Typesence\Lib\Configuration  $config
     * @param   string                                 $name
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