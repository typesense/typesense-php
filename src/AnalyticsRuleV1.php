<?php

namespace Typesense;

class AnalyticsRuleV1
{
    private $ruleName;
    private ApiCall $apiCall;

    public function __construct(string $ruleName, ApiCall $apiCall)
    {
        $this->ruleName = $ruleName;
        $this->apiCall  = $apiCall;
    }

    /**
     * Retrieve a legacy v1 analytics rule by name.
     *
     * @example
     * $client->analyticsV1->rules()['rule-1']->retrieve()
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     */
    public function retrieve()
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * Delete a legacy v1 analytics rule by name.
     *
     * @example
     * $client->analyticsV1->rules()['rule-1']->delete()
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     */
    public function delete()
    {
        return $this->apiCall->delete($this->endpointPath());
    }

    private function endpointPath()
    {
        return AnalyticsRulesV1::RESOURCE_PATH . '/' . encodeURIComponent($this->ruleName);
    }
}
