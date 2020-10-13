<?php

namespace Typesense;

use Typesense\Exceptions\ConfigError;
use Typesense\Lib\Configuration;

/**
 * Class Client
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Client
{

    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var Collections
     */
    public Collections $collections;

    /**
     * @var Aliases
     */
    public Aliases $aliases;

    /**
     * @var Keys
     */
    public Keys $keys;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Client constructor.
     *
     * @param array $config
     *
     * @throws ConfigError
     */
    public function __construct(array $config)
    {
        $this->config  = new Configuration($config);
        $this->apiCall = new ApiCall($this->config);

        $this->collections = new Collections($this->apiCall);
        $this->aliases     = new Aliases($this->apiCall);
        $this->keys        = new Keys($this->apiCall);
    }

    /**
     * @return Collections
     */
    public function getCollections(): Collections
    {
        return $this->collections;
    }

    /**
     * @return Aliases
     */
    public function getAliases(): Aliases
    {
        return $this->aliases;
    }

    /**
     * @return Keys
     */
    public function getKeys(): Keys
    {
        return $this->keys;
    }
}
