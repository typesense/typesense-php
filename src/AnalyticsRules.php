<?php

namespace Typesense;

class AnalyticsRules
{
    const RESOURCE_PATH = '/analytics/rules';

    private ApiCall $apiCall;
    private $analyticsRules = [];

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    public function __get($ruleName)
    {
        if (!isset($this->analyticsRules[$ruleName])) {
            $this->analyticsRules[$ruleName] = new AnalyticsRule($ruleName, $this->apiCall);
        }
        return $this->analyticsRules[$ruleName];
    }

    public function upsert($ruleName, $params)
    {
        return $this->apiCall->put($this->endpoint_path($ruleName), $params);
    }

    public function retrieve()
    {
        return $this->apiCall->get($this->endpoint_path(), []);
    }

    private function endpoint_path($operation = null)
    {
        return self::RESOURCE_PATH . ($operation === null ? '' : "/$operation");
    }
}
