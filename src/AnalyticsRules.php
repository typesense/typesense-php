<?php

namespace Typesense;

class AnalyticsRules implements \ArrayAccess
{
    const RESOURCE_PATH = '/analytics/rules';

    private ApiCall $apiCall;

    /**
     * @var array
     */
    private array $analyticsRules = [];

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
        if (isset($this->{$ruleName})) {
            return $this->{$ruleName};
        }
        if (!isset($this->analyticsRules[$ruleName])) {
            $this->analyticsRules[$ruleName] = new AnalyticsRule($ruleName, $this->apiCall);
        }

        return $this->analyticsRules[$ruleName];
    }

    /**
     * ArrayAccess implementation for backwards compatibility
     */
    public function offsetExists($offset): bool
    {
        return isset($this->analyticsRules[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): AnalyticsRule
    {
        if (!isset($this->analyticsRules[$offset])) {
            $this->analyticsRules[$offset] = new AnalyticsRule($offset, $this->apiCall);
        }

        return $this->analyticsRules[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->analyticsRules[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->analyticsRules[$offset]);
    }
} 