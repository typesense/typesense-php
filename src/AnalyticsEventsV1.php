<?php

namespace Typesense;

/**
 * Class AnalyticsEventsV1
 *
 * @package \Typesense
 */
class AnalyticsEventsV1
{
    const RESOURCE_PATH = '/analytics/events';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * AnalyticsEventsV1 constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Submit a legacy v1 analytics event.
     *
     * @example
     * $client->analyticsV1->events()->create(['type' => 'click', 'name' => 'products_click', 'data' => []])
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     *
     * @param array $params
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function create($params)
    {
        return $this->apiCall->post($this->endpoint_path(), $params);
    }

    /**
     * @return string
     */
    private function endpoint_path($operation = null)
    {
        return self::RESOURCE_PATH . ($operation === null ? '' : "/$operation");
    }
}
