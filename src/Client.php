<?php

namespace Typesense;

use \Typesense\Lib\Configuration;

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
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var \Typesense\Collections
     */
    public Collections $collections;

    /**
     * @var \Typesense\Aliases
     */
    public Aliases $aliases;

    /**
     * Client constructor.
     *
     * @param  array  $config
     *
     * @throws \Typesense\Exceptions\ConfigError
     */
    public function __construct(array $config)
    {
        $this->config      = new Configuration($config);
        $this->collections = new Collections($this->config);
        $this->aliases     = new Aliases($this->config);
    }

    /**
     * @return \Typesense\Collections
     */
    public function getCollections(): Collections
    {
        return $this->collections;
    }

    /**
     * @return \Typesense\Aliases
     */
    public function getAliases(): Aliases
    {
        return $this->aliases;
    }

}