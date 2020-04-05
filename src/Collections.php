<?php


namespace Devloops\Typesence;

use Devloops\Typesence\Lib\Configuration;

/**
 * Class Collections
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Collections
{

    public const RESOURCE_PATH = '/collections';

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $congif;

    private $apiCall;

    /**
     * @var array
     */
    private $collections = [];

    /**
     * Collections constructor.
     *
     * @param $congif
     */
    public function __construct(Configuration $congif)
    {
        $this->congif  = $congif;
        $this->apiCall = new ApiCall($congif);
    }

    /**
     * @param $collectionName
     *
     * @return mixed
     */
    public function __get($collectionName)
    {
        if (isset($this->{$collectionName})) {
            return $this->{$collectionName};
        }
        if (!isset($this->collections[$collectionName])) {
            $this->collections[$collectionName] =
              new Collection($this->congif, $collectionName);
        }

        return $this->collections[$collectionName];
    }

    /**
     * @param   array  $schema
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $schema): array
    {
        return $this->apiCall->post(self::RESOURCE_PATH, $schema);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function retrieve(): array
    {
        return $this->apiCall->get(self::RESOURCE_PATH, []);
    }

}