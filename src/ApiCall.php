<?php

namespace Typesense;

use Exception;
use Http\Client\Exception as HttpClientException;
use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Psr\Log\LoggerInterface;
use Typesense\Exceptions\HTTPStatus0Error;
use Typesense\Exceptions\ObjectAlreadyExists;
use Typesense\Exceptions\ObjectNotFound;
use Typesense\Exceptions\ObjectUnprocessable;
use Typesense\Exceptions\RequestMalformed;
use Typesense\Exceptions\RequestUnauthorized;
use Typesense\Exceptions\ServerError;
use Typesense\Exceptions\ServiceUnavailable;
use Typesense\Exceptions\TypesenseClientError;
use Typesense\Lib\Configuration;
use Typesense\Lib\Node;

/**
 * Class ApiCall
 *
 * @package \Typesense
 * @date    4/5/20
 * @author  Abdullah Al-Faqeir <abdullah@devloops.net>
 */
class ApiCall
{

    private const API_KEY_HEADER_NAME = 'X-TYPESENSE-API-KEY';

    /**
     * @var HttpClient
     */
    private HttpClient $client;

    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @var array|Node[]
     */
    private static array $nodes;

    /**
     * @var Node|null
     */
    private static ?Node $nearestNode;

    /**
     * @var int
     */
    private int $nodeIndex;

    /**
     * @var LoggerInterface
     */
    public LoggerInterface $logger;

    /**
     * ApiCall constructor.
     *
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config      = $config;
        $this->logger      = $config->getLogger();
        $this->client      = $config->getClient();
        static::$nodes       = $this->config->getNodes();
        static::$nearestNode = $this->config->getNearestNode();
        $this->nodeIndex   = 0;
        $this->initializeNodes();
    }

    /**
     *  Initialize Nodes
     */
    private function initializeNodes(): void
    {
        if (static::$nearestNode !== null) {
            $this->setNodeHealthCheck(static::$nearestNode, true);
        }

        foreach (static::$nodes as &$node) {
            $this->setNodeHealthCheck($node, true);
        }
    }

    /**
     * @param string $endPoint
     * @param array $params
     * @param bool $asJson
     *
     * @return string|array
     * @throws TypesenseClientError
     * @throws Exception|HttpClientException
     */
    public function get(string $endPoint, array $params, bool $asJson = true)
    {
        return $this->makeRequest('get', $endPoint, $asJson, [
            'query' => $params ?? [],
        ]);
    }

    /**
     * @param string $endPoint
     * @param mixed $body
     *
     * @param bool $asJson
     * @param array $queryParameters
     *
     * @return array|string
     * @throws TypesenseClientError
     * @throws HttpClientException
     */
    public function post(string $endPoint, $body, bool $asJson = true, array $queryParameters = [])
    {
        return $this->makeRequest('post', $endPoint, $asJson, [
            'data' => $body ?? [],
            'query' => $queryParameters ?? []
        ]);
    }

    /**
     * @param string $endPoint
     * @param array $body
     *
     * @param bool $asJson
     * @param array $queryParameters
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function put(string $endPoint, array $body, bool $asJson = true, array $queryParameters = []): array
    {
        return $this->makeRequest('put', $endPoint, $asJson, [
            'data' => $body ?? [],
            'query' => $queryParameters ?? []
        ]);
    }

    /**
     * @param string $endPoint
     * @param array $body
     *
     * @param bool $asJson
     * @param array $queryParameters
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function patch(string $endPoint, array $body, bool $asJson = true, array $queryParameters = []): array
    {
        return $this->makeRequest('patch', $endPoint, $asJson, [
            'data' => $body ?? [],
            'query' => $queryParameters ?? []
        ]);
    }

    /**
     * @param string $endPoint
     *
     * @param bool $asJson
     * @param array $queryParameters
     *
     * @return array
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(string $endPoint, bool $asJson = true, array $queryParameters = []): array
    {
        return $this->makeRequest('delete', $endPoint, $asJson, [
            'query' => $queryParameters ?? []
        ]);
    }

    /**
     * Makes the actual http request, along with retries
     *
     * @param string $method
     * @param string $endPoint
     * @param bool $asJson
     * @param array $options
     *
     * @return string|array
     * @throws TypesenseClientError|HttpClientException
     * @throws Exception
     */
    private function makeRequest(string $method, string $endPoint, bool $asJson, array $options)
    {
        $numRetries    = 0;
        $lastException = null;
        while ($numRetries < $this->config->getNumRetries() + 1) {
            $numRetries++;
            $node = $this->getNode();

            try {
                $url   = $node->url() . $endPoint;
                $reqOp = $this->getRequestOptions();
                if (isset($options['data'])) {
                    if (is_string($options['data'])) {
                        $reqOp['body'] = $options['data'];
                    } else {
                        $reqOp['json'] = $options['data'];
                    }
                }

                if (isset($options['query'])) {
                    foreach ($options['query'] as $key => $value) :
                        if (is_bool($value)) {
                            $options['query'][$key] = ($value) ? 'true' : 'false';
                        }
                    endforeach;
                    $reqOp['query'] = http_build_query($options['query']);
                }

                $response = $this->client->send(\strtoupper($method), $url, $reqOp);

                $statusCode = $response->getStatusCode();
                if (0 < $statusCode && $statusCode < 500) {
                    $this->setNodeHealthCheck($node, true);
                }

                if (!(200 <= $statusCode && $statusCode < 300)) {
                    $errorMessage = json_decode($response->getBody()
                            ->getContents(), true, 512, JSON_THROW_ON_ERROR)['message'] ?? 'API error.';
                    throw $this->getException($statusCode)
                        ->setMessage($errorMessage);
                }

                return $asJson ? json_decode($response->getBody()
                    ->getContents(), true, 512, JSON_THROW_ON_ERROR) : $response->getBody()
                    ->getContents();
            } catch (HttpException $exception) {
                if (
                    $exception->getResponse()
                        ->getStatusCode() === 408
                ) {
                    continue;
                }
                $this->setNodeHealthCheck($node, false);
                throw $this->getException($exception->getResponse()
                    ->getStatusCode())
                    ->setMessage($exception->getMessage());
            } catch (TypesenseClientError | HttpClientException $exception) {
                $this->setNodeHealthCheck($node, false);
                throw $exception;
            } catch (Exception $exception) {
                $this->setNodeHealthCheck($node, false);
                $lastException = $exception;
                sleep($this->config->getRetryIntervalSeconds());
            }
        }

        if ($lastException) {
            throw $lastException;
        }
    }

    /**
     * @return array
     */
    private function getRequestOptions(): array
    {
        return [
            'headers' => [
                static::API_KEY_HEADER_NAME => $this->config->getApiKey(),
            ],
            'connect_timeout' => $this->config->getConnectionTimeoutSeconds(),
        ];
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function nodeDueForHealthCheck(Node $node): bool
    {
        $currentTimestamp = time();
        return ($currentTimestamp - $node->getLastAccessTs()) > $this->config->getHealthCheckIntervalSeconds();
    }

    /**
     * @param Node $node
     * @param bool $isHealthy
     */
    public function setNodeHealthCheck(Node $node, bool $isHealthy): void
    {
        $node->setHealthy($isHealthy);
        $node->setLastAccessTs(time());
    }

    /**
     * Returns a healthy host from the pool in a round-robin fashion
     * Might return an unhealthy host periodically to check for recovery.
     *
     * @return Node
     */
    public function getNode(): Lib\Node
    {
        if (static::$nearestNode !== null) {
            if (static::$nearestNode->isHealthy() || $this->nodeDueForHealthCheck(static::$nearestNode)) {
                return static::$nearestNode;
            }
        }
        $i = 0;
        while ($i < count(static::$nodes)) {
            $i++;
            $node            = static::$nodes[$this->nodeIndex];
            $this->nodeIndex = ($this->nodeIndex + 1) % count(static::$nodes);
            if ($node->isHealthy() || $this->nodeDueForHealthCheck($node)) {
                return $node;
            }
        }

        /**
         * None of the nodes are marked healthy, but some of them could have become healthy since last health check.
         * So we will just return the next node.
         */
        return static::$nodes[$this->nodeIndex];
    }

    /**
     * @param int $httpCode
     *
     * @return TypesenseClientError
     */
    public function getException(int $httpCode): TypesenseClientError
    {
        switch ($httpCode) {
            case 0:
                return new HTTPStatus0Error();
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

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
