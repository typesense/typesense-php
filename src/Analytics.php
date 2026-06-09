<?php

namespace Typesense;

class Analytics
{
    const RESOURCE_PATH = '/analytics';

    private ApiCall $apiCall;

    private AnalyticsRules $rules;

    private AnalyticsEvents $events;

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Access the analytics rules resource. Use as a method-style endpoint to list or create rules,
     * or chain `[$id]` on the returned object to access a single rule.
     *
     * @example
     * $client->analytics->rules()->retrieve()
     * @example
     * $client->analytics->rules()['rule-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     */
    public function rules()
    {
        if (!isset($this->rules)) {
            $this->rules = new AnalyticsRules($this->apiCall);
        }
        return $this->rules;
    }

    /**
     * Access the analytics events resource to send analytics events.
     *
     * @example
     * $client->analytics->events()->create(['type' => 'click', 'name' => 'products_click', 'data' => []])
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     */
    public function events()
    {
        if (!isset($this->events)) {
            $this->events = new AnalyticsEvents($this->apiCall);
        }
        return $this->events;
    }
} 