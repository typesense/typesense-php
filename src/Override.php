<?php


namespace Devloops\Typesence;

/**
 * Class Override
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class Override
{

    private $config;

    private $collectionName;

    private $overrideId;

    private $apiCall;

    /**
     * Override constructor.
     *
     * @param   \Devloops\Typesence\Configuration  $config
     * @param   string                             $collectionName
     * @param   int                                $overrideId
     */
    public function __construct(
      Configuration $config,
      string $collectionName,
      int $overrideId
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
        return sprintf('%s/%s/%s/%s', Collections::RESOURCE_PATH,
          $this->collectionName, Overrides::RESOURCE_PATH, $this->overrideId);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function retrieve(): array
    {
        return $this->apiCall->get($this->endPointPath(), []);
    }

    /**
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): array
    {
        return $this->apiCall->delete($this->endPointPath());
    }

}