<?php

namespace Typesense;

use Typesense\Exceptions\ConfigError;
use Typesense\Lib\Configuration;

include('utils/utils.php');
/**
 * Typesense client for indexing, searching, and managing collections.
 *
 * @see https://typesense.org/docs/latest/api/
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Client
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * Access the collections resource. Use as a method-style endpoint to list or create collections,
     * or as an array to access a single collection by name.
     *
     * @example
     * $client->collections->create(['name' => 'products', 'fields' => [['name' => 'title', 'type' => 'string']]])
     * @example
     * $client->collections['products']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/collections.html
     *
     * @var Collections
     */
    public Collections $collections;

    /**
     * Manage stopwords sets used at query time.
     *
     * @example
     * $client->stopwords->put(['name' => 'en', 'stopwords' => ['a', 'the']])
     *
     * @see https://typesense.org/docs/latest/api/stopwords.html
     *
     * @var Stopwords
     */
    public Stopwords $stopwords;

    /**
     * Access the aliases resource. Use it to upsert or list aliases, or as an array
     * to access a single alias by name.
     *
     * @example
     * $client->aliases->upsert('my-alias', ['collection_name' => 'products'])
     * @example
     * $client->aliases['my-alias']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/collection-alias.html
     *
     * @var Aliases
     */
    public Aliases $aliases;

    /**
     * Access the API keys resource. Use it to create or list keys, or as an array
     * to access a single key by ID.
     *
     * @example
     * $client->keys->create(['description' => 'Search-only key', 'actions' => ['documents:search'], 'collections' => ['*']])
     * @example
     * $client->keys[1]->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/api-keys.html
     *
     * @var Keys
     */
    public Keys $keys;

    /**
     * Retrieve server version and state information.
     *
     * @example
     * $client->debug->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#debug
     *
     * @var Debug
     */
    public Debug $debug;

    /**
     * Get current RAM, CPU, Disk & Network usage metrics.
     *
     * @example
     * $client->metrics->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html
     *
     * @var Metrics
     */
    public Metrics $metrics;

    /**
     * Checks if Typesense server is ready to accept requests.
     *
     * @example
     * $client->health->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html#health
     *
     * @var Health
     */
    public Health $health;

    /**
     * Cluster operations: snapshots, voting, cache, on-disk compaction, slow request log.
     *
     * @example
     * $client->operations->perform('snapshot', ['snapshot_path' => '/tmp/snap'])
     *
     * @see https://typesense.org/docs/latest/api/cluster-operations.html
     *
     * @var Operations
     */
    public Operations $operations;

    /**
     * Send multiple search requests in a single HTTP request.
     *
     * @example
     * $client->multiSearch->perform(['searches' => [['collection' => 'products', 'q' => '*']]])
     *
     * @see https://typesense.org/docs/latest/api/documents.html#federated-multi-search
     *
     * @var MultiSearch
     */
    public MultiSearch $multiSearch;

    /**
     * Access the presets resource. Use it to upsert or list presets, or as an array
     * to access a single preset by name.
     *
     * @example
     * $client->presets->upsert('listing_view', ['value' => ['q' => '*']])
     * @example
     * $client->presets['listing_view']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/search.html#presets
     *
     * @var Presets
     */
    public Presets $presets;

    /**
     * Legacy v1 analytics API for rules and events.
     *
     * @example
     * $client->analyticsV1->rules()->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     *
     * @var AnalyticsV1
     */
    public AnalyticsV1 $analyticsV1;

    /**
     * Manage analytics rules and events.
     *
     * @example
     * $client->analytics->rules()->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/analytics-query-suggestions.html
     *
     * @var Analytics
     */
    public Analytics $analytics;

    /**
     * Manage stemming dictionaries.
     *
     * @example
     * $client->stemming->dictionaries()->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/stemming.html
     *
     * @var Stemming
     */
    public Stemming $stemming;

    /**
     * Access the conversation models resource and individual conversations.
     *
     * @example
     * $client->conversations->typesenseModels->retrieve()
     * @example
     * $client->conversations['conv-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/conversational-search-rag.html
     *
     * @var Conversations
     */
    public Conversations $conversations;

    /**
     * Access the NL search models resource. Use it to create or list models, or as an array
     * to access a single model by ID.
     *
     * @example
     * $client->nlSearchModels->create(['model_name' => 'openai/gpt-4'])
     * @example
     * $client->nlSearchModels['model-1']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/natural-language-search.html
     *
     * @var NLSearchModels
     */
    public NLSearchModels $nlSearchModels;

    /**
     * Access the synonym sets resource. Use it to upsert or list sets, or as an array
     * to access a single set by name.
     *
     * @example
     * $client->synonymSets->retrieve()
     * @example
     * $client->synonymSets['my-set']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/synonyms.html
     *
     * @var SynonymSets
     */
    public SynonymSets $synonymSets;

    /**
     * Access the curation sets resource. Use it to upsert or list sets, or as an array
     * to access a single set by name.
     *
     * @example
     * $client->curationSets->retrieve()
     * @example
     * $client->curationSets['my-set']->retrieve()
     *
     * @see https://typesense.org/docs/latest/api/curation.html
     *
     * @var CurationSets
     */
    public CurationSets $curationSets;

    /**
     * @var ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Client constructor.
     *
     * @param array $config
     *
     * @throws ConfigError
     */
    public function __construct(array $config)
    {
        $this->config  = new Configuration($config);
        $this->apiCall = new ApiCall($this->config);

        $this->collections   = new Collections($this->apiCall);
        $this->stopwords     = new Stopwords($this->apiCall);
        $this->aliases       = new Aliases($this->apiCall);
        $this->keys          = new Keys($this->apiCall);
        $this->debug         = new Debug($this->apiCall);
        $this->metrics       = new Metrics($this->apiCall);
        $this->health        = new Health($this->apiCall);
        $this->operations    = new Operations($this->apiCall);
        $this->multiSearch   = new MultiSearch($this->apiCall);
        $this->presets       = new Presets($this->apiCall);
        $this->analytics     = new Analytics($this->apiCall);
        $this->analyticsV1   = new AnalyticsV1($this->apiCall);
        $this->stemming     = new Stemming($this->apiCall);
        $this->conversations = new Conversations($this->apiCall);
        $this->nlSearchModels = new NLSearchModels($this->apiCall);
        $this->synonymSets = new SynonymSets($this->apiCall);
        $this->curationSets = new CurationSets($this->apiCall);
    }

    /**
     * @return Collections
     */
    public function getCollections(): Collections
    {
        return $this->collections;
    }

    /**
     * @return Stopwords
     */
    public function getStopwords(): Stopwords
    {
        return $this->stopwords;
    }

    /**
     * @return Aliases
     */
    public function getAliases(): Aliases
    {
        return $this->aliases;
    }

    /**
     * @return Keys
     */
    public function getKeys(): Keys
    {
        return $this->keys;
    }

    /**
     * @return Debug
     */
    public function getDebug(): Debug
    {
        return $this->debug;
    }

    /**
     * @return Metrics
     */
    public function getMetrics(): Metrics
    {
        return $this->metrics;
    }

    /**
     * @return Health
     */
    public function getHealth(): Health
    {
        return $this->health;
    }

    /**
     * @return Operations
     */
    public function getOperations(): Operations
    {
        return $this->operations;
    }

    /**
     * @return MultiSearch
     */
    public function getMultiSearch(): MultiSearch
    {
        return $this->multiSearch;
    }

    /**
     * @return Presets
     */
    public function getPresets(): Presets
    {
        return $this->presets;
    }

    /**
     * @return Analytics
     */
    public function getAnalytics(): Analytics
    {
        return $this->analytics;
    }

    /**
     * @return Stemming
     */
    public function getStemming(): Stemming
    {
        return $this->stemming;
    }

    /**
     * @return Conversations
     */
    public function getConversations(): Conversations
    {
        return $this->conversations;
    }

    /**
     * @return NLSearchModels
     */
    public function getNLSearchModels(): NLSearchModels
    {
        return $this->nlSearchModels;
    }

    /**
     * @return SynonymSets
     */
    public function getSynonymSets(): SynonymSets
    {
        return $this->synonymSets;
    }

    /**
     * @return CurationSets
     */
    public function getCurationSets(): CurationSets
    {
        return $this->curationSets;
    }
}
