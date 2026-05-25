<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Stopwords
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Stopwords
{
    /**
     * @var ApiCall
     */
    private $apiCall;

    public const STOPWORDS_PATH = '/stopwords';

    /**
     * Stopwords constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
     * Retrieve the details of a stopwords set, given its name.
     *
     * @example
     * $client->stopwords->get('en')
     *
     * @see https://typesense.org/docs/latest/api/stopwords.html
     *
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function get(string $stopwordsName)
    {
        return $this->apiCall->get(
            $this->endpointPath($stopwordsName),
            []
        );
    }

    /**
     * Retrieve the details of all stopwords sets.
     *
     * @example
     * $client->stopwords->getAll()
     *
     * @see https://typesense.org/docs/latest/api/stopwords.html
     *
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function getAll()
    {
        return $this->apiCall->get(static::STOPWORDS_PATH, []);
    }

    /**
     * Upsert a stopwords set. The set's name must be provided as the `name` key on the array.
     *
     * @example
     * $client->stopwords->put(['name' => 'en', 'stopwords' => ['a', 'the']])
     *
     * @see https://typesense.org/docs/latest/api/stopwords.html
     *
     * @param array $stopwordSet
     *
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function put(array $stopwordSet)
    {
        return $this->apiCall->put($this->endpointPath($stopwordSet['name']), $stopwordSet);
    }

    /**
     * Permanently deletes a stopwords set, given its name.
     *
     * @example
     * $client->stopwords->delete('en')
     *
     * @see https://typesense.org/docs/latest/api/stopwords.html
     *
     * @param $stopwordsName
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function delete($stopwordsName)
    {
        return $this->apiCall->delete(
            $this->endpointPath($stopwordsName)
        );
    }

    /**
     * @param $stopwordsName
     * @return string
     */
    private function endpointPath($stopwordsName)
    {
        return sprintf(
            '%s/%s',
            static::STOPWORDS_PATH,
            encodeURIComponent($stopwordsName)
        );
    }
}
