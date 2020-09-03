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
    private $path;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var bool
     */
    private $healthy = false;

    /**
     * @var int
     */
    private $lastAccessTs;

    /**
     * Node constructor.
     *
     * @param  string  $host
     * @param  string  $port
     * @param  string  $path
     * @param  string  $protocol
     */
    public function __construct(
      string $host, string $port, string $path, string $protocol
    ) {
        $this->host         = $host;
        $this->port         = $port;
        $this->path         = $path;
        $this->protocol     = $protocol;
        $this->lastAccessTs = time();
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return sprintf('%s://%s:%s%s', $this->protocol, $this->host, $this->port, $this->path);
    }

    /**
     * @return bool
     */
    public function isHealthy(): bool
    {
        return $this->healthy;
    }

    /**
     * @param  bool  $healthy
     */
    public function setHealthy(bool $healthy): void
    {
        $this->healthy = $healthy;
    }

    /**
     * @return int
     */
    public function getLastAccessTs(): int
    {
        return $this->lastAccessTs;
    }

    /**
     * @param  int  $lastAccessTs
     */
    public function setLastAccessTs(int $lastAccessTs): void
    {
        $this->lastAccessTs = $lastAccessTs;
    }

}