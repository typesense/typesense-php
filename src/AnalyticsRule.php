<?php

namespace Typesense;

class AnalyticsRule
{
    private string $ruleName;
    private ApiCall $apiCall;

    public function __construct(string $ruleName, ApiCall $apiCall)
    {
        $this->ruleName = $ruleName;
        $this->apiCall = $apiCall;
    }

    /**
     * Retrieve the details of an analytics rule, given its name.
     *
     * @example
     * $client->analytics->rules()['rule-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     *
     * @return array Response from the API
     */
    public function retrieve()
    {
        return $this->apiCall->get($this->endpointPath(), []);
    }

    /**
     * Permanently deletes an analytics rule, given its name.
     *
     * @example
     * $client->analytics->rules()['rule-1']->delete()
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     *
     * @return array Response from the API
     */
    public function delete()
    {
        return $this->apiCall->delete($this->endpointPath());
    }

    /**
     * Upserts an analytics rule with the given name.
     *
     * @example
     * $client->analytics->rules()['rule-1']->update(['type' => 'popular_queries', 'params' => []])
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
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
        return AnalyticsRules::RESOURCE_PATH . '/' . encodeURIComponent($this->ruleName);
    }
} 