<?php

namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Client
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Client
{

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var \Devloops\Typesence\Collections
     */
    public $collections;

    /**
     * @var \Devloops\Typesence\Aliases
     */
    public $aliases;

    /**
     * Client constructor.
     *
     * @param  array  $config
     *
     * @throws \Devloops\Typesence\Exceptions\ConfigError
     */
    public function __construct(array $config)
    {
        $this->config      = new Configuration($config);
        $this->collections = new Collections($this->config);
        $this->aliases     = new Aliases($this->config);
    }

    /**
     * @return \Devloops\Typesence\Collections
     */
    public function getCollections(): Collections
    {
        return $this->collections;
    }

    /**
     * @return \Devloops\Typesence\Aliases
     */
    public function getAliases(): Aliases
    {
        return $this->aliases;
    }

}