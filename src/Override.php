<?php


namespace Typesense;

use \Typesense\Lib\Configuration;

/**
 * Class Override
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Override
{

    /**
     * @var \Typesense\Lib\Configuration
     */
    private Configuration $config;

    /**
     * @var string
     */
    private string $collectionName;

    /**
     * @var string
     */
    private string $overrideId;

    /**
     * @var \Typesense\ApiCall
     */
    private ApiCall $apiCall;

    /**
     * Override constructor.
     *
     * @param  \Typesense\Lib\Configuration  $config
     * @param  string  $collectionName
     * @param  int  $overrideId
     */
    public function __construct(
      Configuration $config, string $collectionName, string $overrideId
    ) {
        $this->config         = $config;
        $this->collectionName = $collectionName;
        $this->overrideId     = $overrideId;
        $this->apiCall        = new ApiCall($this->config);
    }

    /**
     * @return string
     */
    private function endPointPath(): string
    {
        return sprintf('%s/%s/%s/%s', Collections::RESOURCE_PATH, $this->collectionName, Overrides::RESOURCE_PATH, $this->overrideId);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws \Typesense\Exceptions\TypesenseClientError|\GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

}