<?php

namespace Typesense;

use Http\Client\Exception as HttpClientException;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class Document
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
     * Document constructor.
     *
     * @param ApiCall $apiCall
     */
    public function __construct(ApiCall $apiCall)
    {
        $this->apiCall = $apiCall;
    }

    /**
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
     * @return array|string
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function getAll()
    {
        return $this->apiCall->get(static::STOPWORDS_PATH, []);
    }

    /**
     * @param array $options
     *
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function put(array $options = [])
    {
        $stopwordsName = $options['stopwords_name'];
        $stopwordsData = $options['stopwords_data'];
        return $this->apiCall->put(
            $this->endpointPath($stopwordsName),
            ['stopwords' => $stopwordsData]
        );
    }

    /**
     * @param $presetName
     * @return array
     * @throws HttpClientException
     * @throws TypesenseClientError
     */
    public function delete($presetName)
    {
        return $this->apiCall->delete(
            $this->endpointPath($presetName)
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
            $stopwordsName
        );
    }
}
