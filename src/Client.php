<?php

namespace Typesence;

use \Typesence\Lib\Configuration;

/**
 * Class Client
 *
 * @package \Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Client
{

    /**
     * @var \Typesence\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var \Typesence\Collections
     */
    public Collections $collections;

    /**
     * @var \Typesence\Aliases
     */
    public Aliases $aliases;

    /**
     * Client constructor.
     *
     * @param  array  $config
     *
     * @throws \Typesence\Exceptions\ConfigError
     */
    public function __construct(array $config)
    {
        $this->config      = new Configuration($config);
        $this->collections = new Collections($this->config);
        $this->aliases     = new Aliases($this->config);
    }

    /**
     * @return \Typesence\Collections
     */
    public function getCollections(): Collections
    {
        return $this->collections;
    }

    /**
     * @return \Typesence\Aliases
     */
    public function getAliases(): Aliases
    {
        return $this->aliases;
    }

}