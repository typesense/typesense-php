<?php

namespace Typesense;

class AnalyticsRules implements \ArrayAccess
{
    const RESOURCE_PATH = '/analytics/rules';

    private ApiCall $apiCall;

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Create multiple analytics rules
     * 
     * @param array $rules Array of rule objects
     * @return array Response from the API
     */
    public function create(array $rules)
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $rules);
    }

    /**
     * Retrieve all analytics rules
     * 
     * @return array Response from the API
     */
    public function retrieve()
    {
        return $this->apiCall->get(self::RESOURCE_PATH, []);
    }

    /**
     * Get a specific rule by name
     * 
     * @param string $ruleName
     * @return AnalyticsRule
     */
    public function __get($ruleName)
    {
        return new AnalyticsRule($ruleName, $this->apiCall);
    }

    /**
     * ArrayAccess implementation for backwards compatibility
     */
    public function offsetExists($offset): bool
    {
        return true; // Rules can be accessed by name
    }

    public function offsetGet($offset): AnalyticsRule
    {
        return new AnalyticsRule($offset, $this->apiCall);
    }

    public function offsetSet($offset, $value): void
    {
        // Not implemented for read-only access
    }

    public function offsetUnset($offset): void
    {
        // Not implemented for read-only access
    }
} 