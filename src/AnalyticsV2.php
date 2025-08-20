<?php

namespace Typesense;

class AnalyticsV2
{
    const RESOURCE_PATH = '/analytics';

    private ApiCall $apiCall;

    private AnalyticsRulesV2 $rules;

    private AnalyticsEventsV2 $events;

    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    public function rules()
    {
        if (!isset($this->rules)) {
            $this->rules = new AnalyticsRulesV2($this->apiCall);
        }
        return $this->rules;
    }

    public function events()
    {
        if (!isset($this->events)) {
            $this->events = new AnalyticsEventsV2($this->apiCall);
        }
        return $this->events;
    }
} 