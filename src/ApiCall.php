<?php

namespace Devloops\Typesence;

use GuzzleHttp\Exception\GuzzleException;
use Devloops\Typesence\Lib\Configuration;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Devloops\Typesence\Exceptions\ServerError;
use Devloops\Typesence\Exceptions\ObjectNotFound;
use Devloops\Typesence\Exceptions\RequestMalformed;
use Devloops\Typesence\Exceptions\ServiceUnavailable;
use Devloops\Typesence\Exceptions\RequestUnauthorized;
use Devloops\Typesence\Exceptions\ObjectAlreadyExists;
use Devloops\Typesence\Exceptions\ObjectUnprocessable;
use Devloops\Typesence\Exceptions\TypesenseClientError;

/**
 * Class ApiCall
 *
 * @package Devloops\Typesence
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class ApiCall
{

    private const API_KEY_HEADER_NAME = 'X-TYPESENSE-API-KEY';

    private const CHECK_FAILED_NODE_INTERVAL_S = 60;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * @var array|\Devloops\Typesence\Lib\Node[]
     */
    private static $nodes;

    /**
     * @var int
     */
    private $nodeIndex;

    /**
     * @var int
     */
    private $lastHealthCheckTs;

    /**
     * ApiCall constructor.
     *
     * @param   \Devloops\Typesence\Lib\Configuration  $config
     */
    public function __construct(Configuration $config)
    {
        $this->config            = $config;
        $this->client            = new \GuzzleHttp\Client();
        self::$nodes             = $this->config->getNodes();
        $this->nodeIndex         = 0;
        $this->lastHealthCheckTs = time();
    }

    /**
     * @param   string  $endPoint
     * @param   array   $params
     * @param   bool    $asJson
     *
     * @return string|array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \Exception
     */
    public function get(string $endPoint, array $params, bool $asJson = true)
    {
        return $this->makeRequest(
          'get',
          $endPoint,
          $asJson,
          [
            'data' => $params ?? [],
          ]
        );
    }

    /**
     * @param   string  $endPoint
     * @param   mixed   $body
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function post(string $endPoint, $body): array
    {
        return $this->makeRequest(
          'post',
          $endPoint,
          true,
          [
            'data' => $body ?? [],
          ]
        );
    }

    /**
     * @param   string  $endPoint
     * @param   array   $body
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function put(string $endPoint, array $body): array
    {
        return $this->makeRequest(
          'put',
          $endPoint,
          true,
          [
            'data' => $body ?? [],
          ]
        );
    }

    /**
     * @param   string  $endPoint
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function delete(string $endPoint): array
    {
        return $this->makeRequest('delete', $endPoint, true, []);
    }

    /**
     * Makes the actual http request, along with retries
     *
     * @param   string  $method
     * @param   string  $endPoint
     * @param   bool    $asJson
     * @param   array   $options
     *
     * @return string|array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    private function makeRequest(
      string $method,
      string $endPoint,
      bool $asJson,
      array $options
    ) {
        $numRetries = 0;
        while ($numRetries < $this->config->getNumRetries()) {
            $numRetries++;
            $node = $this->getNode();
            $node->setHealthy(false);

            try {
                $url   = $node->url() . $endPoint;
                $reqOp = $this->getRequestOptions();
                if (isset($options['data'])) {
                    if ($method === 'get') {
                        $reqOp['query'] = http_build_query($options['data']);
                    } elseif (is_string($options['data'])) {
                        $reqOp['body'] = $options['data'];
                    } else {
                        $reqOp['json'] = $options['data'];
                    }
                }

                $response = $this->client->request($method, $url, $reqOp);

                $statusCode = $response->getStatusCode();
                if (0 < $statusCode && $statusCode < 500) {
                    $node->setHealthy(true);
                }

                if (($method !== 'post' && $statusCode !== 200)
                    || ($method === 'post'
                        && !($statusCode === 200
                             || $statusCode === 201))) {
                    $errorMessage = json_decode(
                                      $response->getBody()->getContents(),
                                      true
                                    )['message'] ?? 'API error.';
                    throw $this->getException($statusCode)->setMessage(
                      $errorMessage
                    );
                }

                return $asJson ? json_decode(
                  $response->getBody()->getContents(),
                  true
                ) : $response->getBody()->getContents();
            } catch (ClientException $exception) {
                if ($exception->getResponse()->getStatusCode() === 408) {
                    continue;
                }
                throw $this->getException(
                  $exception->getResponse()->getStatusCode()
                )->setMessage($exception->getMessage());
            } catch (RequestException $exception) {
                if ($exception->getResponse()->getStatusCode() === 408) {
                    continue;
                }
                throw $this->getException(
                  $exception->getResponse()->getStatusCode()
                )->setMessage($exception->getMessage());
            } catch (TypesenseClientError $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                throw $exception;
            }

            sleep($this->config->getRetryIntervalSeconds());
        }

        throw new TypesenseClientError('Retries exceeded.');
    }

    /**
     * @return array
     */
    private function getRequestOptions(): array
    {
        return [
          'headers'         => [
            self::API_KEY_HEADER_NAME => $this->config->getApiKey(),
          ],
          'connect_timeout' => $this->config->getTimeoutSeconds(),
        ];
    }

    /**
     * @return bool
     */
    private function checkFailedNode(): bool
    {
        $currentTimestamp = time();
        $checkNode        = ($currentTimestamp - $this->lastHealthCheckTs)
                            > self::CHECK_FAILED_NODE_INTERVAL_S;
        if ($checkNode) {
            $this->lastHealthCheckTs = $currentTimestamp;
        }

        return $checkNode;
    }

    /**
     * Returns a healthy host from the pool in a round-robin fashion
     * Might return an unhealthy host periodically to check for recovery.
     *
     * @return \Devloops\Typesence\Lib\Node
     */
    public function getNode(): Lib\Node
    {
        $i = 0;
        while ($i < count(self::$nodes)) {
            $i++;
            $this->nodeIndex = ($this->nodeIndex + 1) % count(self::$nodes);
            if (self::$nodes[$this->nodeIndex]->isHealthy()
                || $this->checkFailedNode()) {
                return self::$nodes[$this->nodeIndex];
            }
        }

        /**
         * None of the nodes are marked healthy, but some of them could have become healthy since last health check.
         * So we will just return the next node.
         */
        $this->nodeIndex = ($this->nodeIndex + 1) % count(self::$nodes);
        return self::$nodes[$this->nodeIndex];
    }

    /**
     * @param   int  $httpCode
     *
     * @return \Devloops\Typesence\Exceptions\TypesenseClientError
     */
    public function getException(int $httpCode): TypesenseClientError
    {
        switch ($httpCode) {
            case 400:
                return new RequestMalformed();
            case 401:
                return new RequestUnauthorized();
            case 404:
                return new ObjectNotFound();
            case 409:
                return new ObjectAlreadyExists();
            case 422:
                return new ObjectUnprocessable();
            case 500:
                return new ServerError();
            case 503:
                return new ServiceUnavailable();
            default:
                return new TypesenseClientError();
        }
    }

}