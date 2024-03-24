<?php

declare(strict_types=1);

namespace Typesense\Requests;

use Typesense\Objects\Metric;
use Typesense\Objects\Stat;

class Cluster extends Request
{
    /**
     * Creates a point-in-time snapshot of a Typesense node's state and data in the specified directory.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#create-snapshot-for-backups
     */
    public function snapshot(string $path): bool
    {
        $path = sprintf('/operations/snapshot?snapshot_path=%s', $path);

        $data = $this->send('POST', $path);

        return $data->success;
    }

    /**
     * Compacting the on-disk database.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#compacting-the-on-disk-database
     */
    public function compact(): bool
    {
        $data = $this->send('POST', '/operations/db/compact');

        return $data->success;
    }

    /**
     * Triggers a follower node to initiate the raft voting process, which triggers leader re-election.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#re-elect-leader
     */
    public function reElectLeader(): bool
    {
        $data = $this->send('POST', '/operations/vote');

        return $data->success;
    }

    /**
     * Enable logging of requests that take over a defined threshold of time.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#toggle-slow-request-log
     */
    public function updateSlowRequestLog(int $ms): bool
    {
        $data = $this->send('POST', '/config', [
            'log-slow-requests-time-ms' => $ms,
        ]);

        return $data->success;
    }

    /**
     * Get current RAM, CPU, Disk & Network usage metrics.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#cluster-metrics
     */
    public function metrics(): Metric
    {
        $data = $this->send('GET', '/metrics.json');

        return Metric::from($data);
    }

    /**
     * Get stats about API endpoints.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#api-stats
     */
    public function stats(): Stat
    {
        $data = $this->send('GET', '/stats.json');

        return Stat::from($data);
    }

    /**
     * Get health information about a Typesense node.
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#health
     */
    public function health(): bool
    {
        $data = $this->send('GET', '/health');

        return $data->ok;
    }
}
