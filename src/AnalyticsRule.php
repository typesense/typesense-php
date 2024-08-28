<?php

namespace Typesense;

class AnalyticsRule
{
    private $ruleName;
    private ApiCall $apiCall;

    public function __construct(string $ruleName, ApiCall $apiCall)
    {
        $this->ruleName = $ruleName;
        $this->apiCall  = $apiCall;
    }

    public function retrieve()
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    public function delete()
    {
        return $this->apiCall->delete($this->endpointPath());
    }

    private function endpointPath()
    {
        return AnalyticsRules::RESOURCE_PATH . '/' . encodeURIComponent($this->ruleName);
    }
}
