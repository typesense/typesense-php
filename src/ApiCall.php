<?php

namespace Devloops\Typesence;

use GuzzleHttp\Exception\GuzzleException;
use Devloops\Typesence\Lib\Configuration;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Devloops\Typesence\Exceptions\ServerError;
use Devloops\Typesence\Exceptions\ObjectNotFound;
use Devloops\Typesence\Exceptions\RequestMalformed;
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

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var \Devloops\Typesence\Lib\Configuration
     */
    private $config;

    /**
     * ApiCall constructor.
     *
     * @param   \Devloops\Typesence\Lib\Configuration  $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @return array
     */
    private function nodes(): array
    {
        return [$this->config->getMasterNode()]
               + $this->config->getReadReplicaNodes();
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
            default:
                return new TypesenseClientError();
        }
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
        $params = $params ?? [];
        foreach ($this->nodes() as $node) {
            $url = $node->url() . $endPoint;
            try {
                $request = $this->client->get(
                  $url,
                  [
                    'headers'         => [
                      self::API_KEY_HEADER_NAME => $node->getApiKey(),
                    ],
                    'query'           => http_build_query($params),
                    'connect_timeout' => $this->config->getTimeoutSeconds(),
                  ]
                );
                if ($request->getStatusCode() !== 200) {
                    $errorMessage = \GuzzleHttp\json_decode(
                                      $request->getBody(),
                                      true
                                    )['message'] ?? 'API error';
                    throw $this->getException($request->getStatusCode())
                               ->setMessage($errorMessage);
                }
                return $asJson ? \GuzzleHttp\json_decode(
                  $request->getBody(),
                  true
                ) : $request->getBody()->getContents();
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
            } catch (GuzzleException $e) {
                continue;
            } catch (TypesenseClientError $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        throw new TypesenseClientError('All hosts are bad');
    }

    /**
     * @param   string  $endPoint
     * @param   array   $body
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $endPoint, array $body): array
    {
        $url    = $this->config->getMasterNode()->url() . $endPoint;
        $apiKey = $this->config->getMasterNode()->getApiKey();
        try {
            $request = $this->client->post(
              $url,
              [
                'headers'         => [
                  self::API_KEY_HEADER_NAME => $apiKey,
                ],
                'json'            => $body,
                'connect_timeout' => $this->config->getTimeoutSeconds(),
              ]
            );
            if ($request->getStatusCode() !== 201) {
                $errorMessage =
                  \GuzzleHttp\json_decode($request->getBody(), true)['message']
                  ?? 'API error';
                throw $this->getException($request->getStatusCode())
                           ->setMessage(
                             $errorMessage
                           );
            }
        } catch (ClientException $exception) {
            throw $this->getException(
              $exception->getResponse()->getStatusCode()
            )->setMessage($exception->getMessage());
        }

        return \GuzzleHttp\json_decode($request->getBody(), true);
    }

    /**
     * @param   string  $endPoint
     * @param   array   $body
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $endPoint, array $body): array
    {
        $url    = $this->config->getMasterNode()->url() . $endPoint;
        $apiKey = $this->config->getMasterNode()->getApiKey();
        try {
            $request = $this->client->put(
              $url,
              [
                'headers'         => [
                  self::API_KEY_HEADER_NAME => $apiKey,
                ],
                'json'            => $body,
                'connect_timeout' => $this->config->getTimeoutSeconds(),
              ]
            );
            if ($request->getStatusCode() !== 200) {
                $errorMessage =
                  \GuzzleHttp\json_decode($request->getBody(), true)['message']
                  ?? 'API error';
                throw $this->getException($request->getStatusCode())
                           ->setMessage(
                             $errorMessage
                           );
            }
        } catch (ClientException $exception) {
            throw $this->getException(
              $exception->getResponse()->getStatusCode()
            )->setMessage($exception->getMessage());
        }

        return \GuzzleHttp\json_decode($request->getBody(), true);
    }

    /**
     * @param   string  $endPoint
     *
     * @return array
     * @throws \Devloops\Typesence\Exceptions\TypesenseClientError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $endPoint): array
    {
        $url    = $this->config->getMasterNode()->url() . $endPoint;
        $apiKey = $this->config->getMasterNode()->getApiKey();
        try {
            $request = $this->client->delete(
              $url,
              [
                'headers'         => [
                  self::API_KEY_HEADER_NAME => $apiKey,
                ],
                'connect_timeout' => $this->config->getTimeoutSeconds(),
              ]
            );
            if ($request->getStatusCode() !== 200) {
                $errorMessage =
                  \GuzzleHttp\json_decode($request->getBody(), true)['message']
                  ?? 'API error';
                throw $this->getException($request->getStatusCode())
                           ->setMessage(
                             $errorMessage
                           );
            }
        } catch (ClientException $exception) {
            throw $this->getException(
              $exception->getResponse()->getStatusCode()
            )->setMessage($exception->getMessage());
        }
        return \GuzzleHttp\json_decode($request->getBody(), true);
    }

}