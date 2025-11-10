<?php

namespace Typesense;

/**
 * Class AnalyticsEvents
 * 
 * Implements the updated analytics events API for Typesense v30.0+
 *
 * @package \Typesense
 */
class AnalyticsEvents
{
    const RESOURCE_PATH = '/analytics/events';

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * AnalyticsEvents constructor.
     *
     * @param ApiCall $apiCall
     */
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