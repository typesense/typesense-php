<?php

namespace Feature;

use Tests\TestCase;
use Typesense\ApiCall;
use Typesense\Lib\Configuration;
use Typesense\Exceptions\ServerError;
use Typesense\Exceptions\RequestMalformed;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\TransferException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;

class ApiCallRetryTest extends TestCase
{
    public function testRetriesOnHttpExceptionWithNon408Status(): void
    {
        $callCount = 0;
        $expectedCalls = 3;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount, $expectedCalls) {
                $callCount++;
                
                if ($callCount < $expectedCalls) {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(500);
                    throw new HttpException('Server error', $this->createMock(RequestInterface::class), $response);
                } else {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(200);
                    $stream = $this->createMock(StreamInterface::class);
                    $stream->method('getContents')->willReturn('{"success": true}');
                    $response->method('getBody')->willReturn($stream);
                    return $response;
                }
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node3', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $result = $apiCall->get('/test', []);
        
        $this->assertEquals(['success' => true], $result);
        $this->assertEquals($expectedCalls, $callCount);
    }

    public function testRetriesExhaustedThrowsLastException(): void
    {
        $callCount = 0;
        $expectedCalls = 3;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(500);
                throw new HttpException('Server error', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $this->expectException(ServerError::class);
        $this->expectExceptionMessage('Server error');
        
        $apiCall->get('/test', []);
        
        $this->assertEquals($expectedCalls, $callCount);
    }

    public function testRetriesOnTypesenseClientError(): void
    {
        $callCount = 0;
        $expectedCalls = 1;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount, $expectedCalls) {
                $callCount++;
                
                if ($callCount < $expectedCalls) {
                    throw new RequestMalformed('Bad request');
                } else {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(200);
                    $stream = $this->createMock(StreamInterface::class);
                    $stream->method('getContents')->willReturn('{"success": true}');
                    $response->method('getBody')->willReturn($stream);
                    return $response;
                }
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node3', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        try {
            $apiCall->get('/test', []);
        } catch (ServerError $e) {
        }
        
        $this->assertEquals($expectedCalls, $callCount);
    }

    public function testRetriesOnHttpClientException(): void
    {
        $callCount = 0;
        $expectedCalls = 3;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount, $expectedCalls) {
                $callCount++;
                
                if ($callCount < $expectedCalls) {
                    throw new TransferException('Connection error');
                } else {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(200);
                    $stream = $this->createMock(StreamInterface::class);
                    $stream->method('getContents')->willReturn('{"success": true}');
                    $response->method('getBody')->willReturn($stream);
                    return $response;
                }
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node3', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $result = $apiCall->get('/test', []);
        
        $this->assertEquals(['success' => true], $result);
        $this->assertEquals($expectedCalls, $callCount);
    }

    public function testSkips408TimeoutErrorsAndContinuesRetrying(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                if ($callCount === 1) {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(408);
                    throw new HttpException('Request timeout', $this->createMock(RequestInterface::class), $response);
                } elseif ($callCount === 2) {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(500);
                    throw new HttpException('Server error', $this->createMock(RequestInterface::class), $response);
                } else {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(200);
                    $stream = $this->createMock(StreamInterface::class);
                    $stream->method('getContents')->willReturn('{"success": true}');
                    $response->method('getBody')->willReturn($stream);
                    return $response;
                }
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node3', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $result = $apiCall->get('/test', []);
        
        $this->assertEquals(['success' => true], $result);
        $this->assertEquals(3, $callCount);
    }

    public function testNodeHealthCheckAfterExceptions(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(500);
                throw new HttpException('Server error', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $node1 = $config->getNodes()[0];
        $node2 = $config->getNodes()[1];
        
        $this->assertTrue($node1->isHealthy());
        $this->assertTrue($node2->isHealthy());
        
        try {
            $apiCall->get('/test', []);
        } catch (ServerError $e) {
            $this->assertFalse($node1->isHealthy());
            $this->assertFalse($node2->isHealthy());
        }
        
        $this->assertEquals(3, $callCount);
    }

    public function test400ErrorsAreNotRetried(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(400);
                throw new HttpException('Bad Request', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $this->expectException(\Typesense\Exceptions\RequestMalformed::class);
        $this->expectExceptionMessage('Bad Request');
        
        $apiCall->get('/test', []);
        
        $this->assertEquals(1, $callCount);
    }

    public function test401ErrorsAreNotRetried(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(401);
                throw new HttpException('Unauthorized', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $this->expectException(\Typesense\Exceptions\RequestUnauthorized::class);
        $this->expectExceptionMessage('Unauthorized');
        
        $apiCall->get('/test', []);
        
        $this->assertEquals(1, $callCount);
    }

    public function test404ErrorsAreNotRetried(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(404);
                throw new HttpException('Not Found', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $this->expectException(\Typesense\Exceptions\ObjectNotFound::class);
        $this->expectExceptionMessage('Not Found');
        
        $apiCall->get('/test', []);
        
        $this->assertEquals(1, $callCount);
    }

    public function test408ErrorsAreSkippedAndRetryingContinues(): void
    {
        $callCount = 0;
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                if ($callCount === 1) {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(408);
                    throw new HttpException('Request timeout', $this->createMock(RequestInterface::class), $response);
                } else {
                    $response = $this->createMock(ResponseInterface::class);
                    $response->method('getStatusCode')->willReturn(200);
                    $stream = $this->createMock(StreamInterface::class);
                    $stream->method('getContents')->willReturn('{"success": true}');
                    $response->method('getBody')->willReturn($stream);
                    return $response;
                }
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http'],
                ['host' => 'node2', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $result = $apiCall->get('/test', []);
        
        $this->assertEquals(['success' => true], $result);
        $this->assertEquals(2, $callCount);
    }

    public function testDoesNotSleepOnFinalRetryAttempt(): void
    {
        $callCount = 0;
        $retryIntervalSeconds = 0.1; 
        
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')
            ->willReturnCallback(function() use (&$callCount) {
                $callCount++;
                
                $response = $this->createMock(ResponseInterface::class);
                $response->method('getStatusCode')->willReturn(500);
                throw new HttpException('Server error', $this->createMock(RequestInterface::class), $response);
            });

        $config = new Configuration([
            'api_key' => 'test-key',
            'nodes' => [
                ['host' => 'node1', 'port' => 8108, 'protocol' => 'http']
            ],
            'num_retries' => 2, 
            'retry_interval_seconds' => $retryIntervalSeconds,
            'client' => $httpClient
        ]);

        $apiCall = new ApiCall($config);
        
        $startTime = microtime(true);
        
        try {
            $apiCall->get('/test', []);
        } catch (ServerError $e) {
        }
        
        $endTime = microtime(true);
        $actualDuration = $endTime - $startTime;
        
        //  2 sleep intervals (between 1st->2nd and 2nd->3rd attempts)
        //  no sleep after the final (3rd) attempt
        $expectedDuration = $retryIntervalSeconds * 2; 
        
        $tolerance = 0.05;
        
        $this->assertEquals(3, $callCount, 'Should make exactly 3 attempts');
        $this->assertLessThan(
            $expectedDuration + $tolerance, 
            $actualDuration,
            "Execution took too long ({$actualDuration}s), suggesting sleep was called on final attempt"
        );
        $this->assertGreaterThan(
            $expectedDuration - $tolerance, 
            $actualDuration,
            "Execution was too fast ({$actualDuration}s), suggesting sleep intervals were skipped"
        );
    }
} 