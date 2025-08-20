<?php

namespace Typesense;

/**
 * Class AnalyticsEventsV2
 * 
 * Implements the updated analytics events API for Typesense v2+
 *
 * @package \Typesense
 */
class AnalyticsEventsV2
{
    const RESOURCE_PATH = '/analytics/events';

    private ApiCall $apiCall;

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Create an analytics event
     * 
     * @param array $params Event parameters including name, event_type, and data
     * @return array Response from the API
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(array $params)
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $params);
    }

    /**
     * Retrieve analytics events
     * 
     * @param array $params Query parameters
     * @return array Response from the API
     */
    public function retrieve(array $params = [])
    {
        return $this->apiCall->get(self::RESOURCE_PATH, $params);
    }
} 