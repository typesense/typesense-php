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
     * @var \Devloops\Typesence\Lib\Node
     */
    private $masterNode;

    /**
     * @var array
     */
    private $readReplicaNodes = [];

    /**
     * @var float
     */
    private $timeoutSeconds;

    /**
     * Configuration constructor.
     *
     * @param   array  $config
     *
     * @throws \Devloops\Typesence\Exceptions\ConfigError
     */
    public function __construct(array $config)
    {
        $this->validateConfigArray($config);

        $masterNodeArray   = $config['master_node'] ?? [];
        $replicaNodeArrays = $config['read_replica_nodes'] ?? [];

        $this->masterNode = new Node(
          $masterNodeArray['host'],
          $masterNodeArray['port'],
          $masterNodeArray['protocol'],
          $masterNodeArray['api_key']
        );

        foreach ($replicaNodeArrays as $replica_node_array) {
            $this->readReplicaNodes[] = new Node(
              $replica_node_array['host'],
              $replica_node_array['port'],
              $replica_node_array['protocol'],
              $replica_node_array['api_key']
            );
        }

        $this->timeoutSeconds = (float)($config['timeout_seconds'] ?? 1.0);
    }


    /**
     * @param   array  $config
     *
     * @throws \Devloops\Typesence\Exceptions\ConfigError
     */
    private function validateConfigArray(array $config): void
    {
        $masterNode = $config['master_node'] ?? false;
        if (!$masterNode) {
            throw new ConfigError('`master_node` is not defined.');
        }

        if (!$this->validateNodeFields($masterNode)) {
            throw new ConfigError(
              '`master_node` must be a dictionary with the following required keys: host, port, protocol, api_key'
            );
        }

        $replicaNodes = $config['read_replica_nodes'] ?? [];
        foreach ($replicaNodes as $replica_node) {
            if (!$this->validateNodeFields($replica_node)) {
                throw new ConfigError(
                  '`read_replica_nodes` entry be a dictionary with the following required keys: host, port, protocol, api_key'
                );
            }
        }
    }

    /**
     * @param   array  $node
     *
     * @return bool
     */
    public function validateNodeFields(array $node): bool
    {
        $keys = ['host', 'port', 'protocol', 'api_key'];
        return !array_diff_key(array_flip($keys), $node);
    }

    /**
     * @return \Devloops\Typesence\Node
     */
    public function getMasterNode(): Node
    {
        return $this->masterNode;
    }

    /**
     * @return array
     */
    public function getReadReplicaNodes(): array
    {
        return $this->readReplicaNodes;
    }

    /**
     * @return float
     */
    public function getTimeoutSeconds(): float
    {
        return $this->timeoutSeconds;
    }

}