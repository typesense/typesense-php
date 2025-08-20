<?php

namespace Typesense;

class AnalyticsRuleV2
{
    private string $ruleName;
    private ApiCall $apiCall;

    public function __construct(string $ruleName, ApiCall $apiCall)
    {
        $this->ruleName = $ruleName;
        $this->apiCall = $apiCall;
    }

    /**
     * Retrieve a specific analytics rule
     * 
     * @return array Response from the API
     */
    public function retrieve()
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * Delete a specific analytics rule
     * 
     * @return array Response from the API
     */
    public function delete()
    {
        return $this->apiCall->delete($this->endpointPath());
    }

    /**
     * Update a specific analytics rule
     * 
     * @param array $params Rule parameters
     * @return array Response from the API
     */
    public function update(array $params)
    {
        return $this->apiCall->put($this->endpointPath(), $params);
    }

    private function endpointPath()
    {
        return AnalyticsRulesV2::RESOURCE_PATH . '/' . encodeURIComponent($this->ruleName);
    }
} 