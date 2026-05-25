<?php

namespace Typesense;

class AnalyticsRulesV1 implements \ArrayAccess
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
            $this->analyticsRules[$ruleName] = new AnalyticsRuleV1($ruleName, $this->apiCall);
        }
        return $this->analyticsRules[$ruleName];
    }

    /**
     * Upsert a legacy v1 analytics rule by name.
     *
     * @example
     * $client->analyticsV1->rules()->upsert('products_query_hits', ['type' => 'popular_queries', 'params' => []])
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     */
    public function upsert($ruleName, $params)
    {
        return $this->apiCall->put($this->endpoint_path($ruleName), $params);
    }

    /**
     * Retrieve all legacy v1 analytics rules.
     *
     * @example
     * $client->analyticsV1->rules()->retrieve()
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     */
    public function retrieve()
    {
        return $this->apiCall->get($this->endpoint_path(), []);
    }

    private function endpoint_path($operation = null)
    {
        return self::RESOURCE_PATH . ($operation === null ? '' : "/" . encodeURIComponent($operation));
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->analyticsRules[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): AnalyticsRuleV1
    {
        if (!isset($this->analyticsRules[$offset])) {
            $this->analyticsRules[$offset] = new AnalyticsRuleV1($offset, $this->apiCall);
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
