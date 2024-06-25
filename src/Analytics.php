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

    public function rules()
    {
        if (!isset($this->rules)) {
            $this->rules = new AnalyticsRules($this->apiCall);
        }
        return $this->rules;
    }

    public function events()
    {
        if (!isset($this->events)) {
            $this->events = new AnalyticsEvents($this->apiCall);
        }
        return $this->events;
    }
}
