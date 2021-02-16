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
     * @var Debug
     */
    public Debug $debug;

    /**
     * @var Metrics
     */
    public Metrics $metrics;

    /**
     * @var Health
     */
    public Health $health;

    /**
     * @var Operations
     */
    public Operations $operations;

    /**
     * @var MultiSearch
     */
    public MultiSearch $multiSearch;

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
        $this->debug       = new Debug($this->apiCall);
        $this->metrics     = new Metrics($this->apiCall);
        $this->health      = new Health($this->apiCall);
        $this->operations  = new Operations($this->apiCall);
        $this->multiSearch = new MultiSearch($this->apiCall);
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

    /**
     * @return Debug
     */
    public function getDebug(): Debug
    {
        return $this->debug;
    }

    /**
     * @return Metrics
     */
    public function getMetrics(): Metrics
    {
        return $this->metrics;
    }

    /**
     * @return Health
     */
    public function getHealth(): Health
    {
        return $this->health;
    }

    /**
     * @return Operations
     */
    public function getOperations(): Operations
    {
        return $this->operations;
    }

    /**
     * @return MultiSearch
     */
    public function getMultiSearch(): MultiSearch
    {
        return $this->multiSearch;
    }
}
