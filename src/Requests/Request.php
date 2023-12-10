<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceAlreadyExistsException;
use Typesense\Exceptions\Client\ResourceNotFoundException;
use Typesense\Exceptions\Client\UnauthorizedException;
use Typesense\Exceptions\Client\UnprocessableEntityException;
use Typesense\Exceptions\MalformedResponsePayloadException;
use Typesense\Exceptions\Server\ServiceUnavailableException;
use Typesense\Exceptions\UnknownHttpException;
use Typesense\Http;

abstract class Request
{
    /**
     * Constructor.
     */
    public function __construct(
        public Http $http,
    ) {
        //
    }

    /**
     * @param  'GET'|'POST'|'PATCH'|'DELETE'  $method
     * @param  ($ndjson is true ? array<int, array<string, mixed>> : array<string, mixed>)  $body
     * @return ($expectArray is false ? stdClass : array<int, stdClass>)
     *
     * @throws UnauthorizedException
     */
    public function send(
        string $method,
        string $path,
        array $body = [],
        bool $expectArray = false,
        bool $ndjson = false,
    ): stdClass|array {
        if (! $ndjson) {
            $form = json_encode($body) ?: '';
        } else {
            $form = array_map(
                fn (array $payload) => json_encode($payload),
                $body,
            );

            $form = implode(PHP_EOL, array_values(array_filter($form)));
        }

        $response = $this->http->request($method, $path, $form);

        $contents = $response->getBody()->getContents();

        $context = json_decode($contents, false);

        $status = $response->getStatusCode();

        if (! ($status >= 200 && $status < 300)) {
            if (! ($context instanceof stdClass) || ! is_string($context->message)) {
                throw new MalformedResponsePayloadException($contents);
            }

            $this->toException($status, $context->message);
        }

        if ($ndjson) {
            return array_map(
                function (string $data) {
                    $result = json_decode($data, false);

                    return $result instanceof stdClass ? $result : new stdClass();
                },
                explode(PHP_EOL, $contents),
            );
        }

        if ($expectArray) {
            if (! is_array($context)) {
                throw new MalformedResponsePayloadException($contents);
            }

            return $context;
        }

        if (! ($context instanceof stdClass)) {
            throw new MalformedResponsePayloadException($contents);
        }

        return $context;
    }

    /**
     * Throw exception by the status code.
     */
    public function toException(int $status, string $message): void
    {
        if ($status === 400) {
            throw new InvalidPayloadException($message);
        }

        if ($status === 401) {
            throw new UnauthorizedException('Missing API key or the API key is invalid.');
        }

        if ($status === 404) {
            throw new ResourceNotFoundException($message);
        }

        if ($status === 409) {
            throw new ResourceAlreadyExistsException($message);
        }

        if ($status === 422) {
            throw new UnprocessableEntityException($message);
        }

        if ($status === 503) {
            throw new ServiceUnavailableException();
        }

        throw new UnknownHttpException();
    }
}
