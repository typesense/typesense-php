<?php

namespace Typesense;

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

    public function rules()
    {
        if (!isset($this->rules)) {
            $this->rules = new AnalyticsRulesV1($this->apiCall);
        }
        return $this->rules;
    }

    public function events()
    {
        if (!isset($this->events)) {
            $this->events = new AnalyticsEventsV1($this->apiCall);
        }
        return $this->events;
    }
}
