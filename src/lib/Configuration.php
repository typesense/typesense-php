<?php

namespace Devloops\Typesence\Lib;

use Devloops\Typesence\Exceptions\ConfigError;

/**
 * Class Configuration
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Configuration
{

    /**
     * @var \Devloops\Typesence\Lib\Node[]
     */
    private $nodes;

    /**
     * @var \Devloops\Typesence\Lib\Node
     */
    private $nearestNode;

    /**
     * @var float
     */
    private $connectionTimeoutSeconds;

    /**
     * @var mixed|string
     */
    private $apiKey;

    /**
     * @var float
     */
    private $numRetries;

    /**
     * @var float
     */
    private $retryIntervalSeconds;

    private $healthcheckIntervalSeconds;

    /**
     * Configuration constructor.
     *
     * @param  array  $config
     *
     * @throws \Devloops\Typesence\Exceptions\ConfigError
     */
    public function __construct(array $config)
    {
        $this->validateConfigArray($config);

        $nodes = $config['nodes'] ?? [];

        foreach ($nodes as $node) {
            $this->nodes[] = new Node($node['host'], $node['port'], $node['path'] ?? '', $node['protocol']);
        }

        $nearestNode = $config['nearest_node'] ?? [];
        if (!empty($nearestNode)) {
            $this->nearestNode = new Node($nearestNode['host'], $nearestNode['port'], $nearestNode['path'] ?? '', $nearestNode['protocol']);
        }

        $this->apiKey                     = $config['api_key'] ?? '';
        $this->connectionTimeoutSeconds   = (float) ($config['connection_timeout_seconds'] ?? 1.0);
        $this->healthcheckIntervalSeconds = (int) ($config['healthcheck_interval_seconds'] ?? 60);
        $this->numRetries                 = (float) ($config['num_retries'] ?? 3);
        $this->retryIntervalSeconds       = (float) ($config['retry_interval_seconds'] ?? 1.0);
    }

    /**
     * @param  array  $config
     *
     * @throws \Devloops\Typesence\Exceptions\ConfigError
     */
    private function validateConfigArray(array $config): void
    {
        $nodes = $config['nodes'] ?? false;
        if (!$nodes) {
            throw new ConfigError('`nodes` is not defined.');
        }

        $apiKey = $config['api_key'] ?? false;
        if (!$apiKey) {
            throw new ConfigError('`api_key` is not defined.');
        }

        foreach ($nodes as $node) {
            if (!$this->validateNodeFields($node)) {
                throw new ConfigError('`node` entry be a dictionary with the following required keys: host, port, protocol, api_key');
            }
        }
        $nearestNode = $config['nearest_node'] ?? [];
        if (!empty($nearestNode) && !$this->validateNodeFields($nearestNode)) {
            throw new ConfigError('`nearest_node` entry be a dictionary with the following required keys: host, port, protocol, api_key');
        }
    }

    /**
     * @param  array  $node
     *
     * @return bool
     */
    public function validateNodeFields(array $node): bool
    {
        $keys = [
          'host',
          'port',
          'protocol',
        ];
        return !array_diff_key(array_flip($keys), $node);
    }

    /**
     * @return \Devloops\Typesence\Lib\Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return \Devloops\Typesence\Lib\Node
     */
    public function getNearestNode(): ?Node
    {
        return $this->nearestNode;
    }

    /**
     * @return mixed|string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return float
     */
    public function getNumRetries(): float
    {
        return $this->numRetries;
    }

    /**
     * @return float
     */
    public function getRetryIntervalSeconds(): float
    {
        return $this->retryIntervalSeconds;
    }

    /**
     * @return float
     */
    public function getConnectionTimeoutSeconds(): float
    {
        return $this->connectionTimeoutSeconds;
    }

    /**
     * @return float|mixed
     */
    public function getHealthcheckIntervalSeconds()
    {
        return $this->healthcheckIntervalSeconds;
    }

}
