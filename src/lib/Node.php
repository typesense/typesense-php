<?php


namespace Devloops\Typesence\Lib;

/**
 * Class Node
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Node
{

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * Node constructor.
     *
     * @param $host
     * @param $port
     * @param $protocol
     * @param $apiKey
     */
    public function __construct($host, $port, $protocol, $apiKey)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->protocol = $protocol;
        $this->apiKey   = $apiKey;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return sprintf('%s://%s:%s', $this->protocol, $this->host, $this->port);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

}