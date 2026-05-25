<?php

namespace Typesense;

/**
 * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->analytics` (new Analytics APIs).
 */
class AnalyticsV1
{
    const RESOURCE_PATH = '/analytics';

    private ApiCall $apiCall;

    private AnalyticsRulesV1 $rules;

    private AnalyticsEventsV1 $events;

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Access the legacy v1 analytics rules resource. Use as a method-style endpoint to list or upsert rules,
     * or chain `[$id]` on the returned object to access a single rule.
     *
     * @example
     * $client->analyticsV1->rules()->retrieve()
     * @example
     * $client->analyticsV1->rules()['rule-1']->retrieve()
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     *
     * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->analytics` (new Analytics APIs).
     */
    public function rules()
    {
        if (!isset($this->rules)) {
            $this->rules = new AnalyticsRulesV1($this->apiCall);
        }
        return $this->rules;
    }

    /**
     * Access the legacy v1 analytics events resource to send analytics events.
     *
     * @example
     * $client->analyticsV1->events()->create(['type' => 'click', 'name' => 'products_click', 'data' => []])
     *
     * @see https://typesense.org/docs/29.0/api/analytics-query-suggestions.html
     *
     * @deprecated Deprecated starting with Typesense Server v30. Please migrate to `$client->analytics` (new Analytics APIs).
     */
    public function events()
    {
        if (!isset($this->events)) {
            $this->events = new AnalyticsEventsV1($this->apiCall);
        }
        return $this->events;
    }
}
