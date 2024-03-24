<?php

declare(strict_types=1);

namespace Typesense\Requests;

use stdClass;
use Typesense\Objects\Document as DocumentObject;
use Typesense\Objects\GenericDocument;
use Typesense\Objects\ImportedDocument;

class Document extends Request
{
    /**
     * @template T of DocumentObject
     *
     * @param  array<string, mixed>  $payload
     * @param  class-string<T>|null  $document
     * @param  'create'|'upsert'  $action
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function index(
        string $collection,
        array $payload,
        ?string $document = null,
        string $action = 'create',
    ): DocumentObject {
        $query = http_build_query([
            'action' => $action,
        ]);

        $path = sprintf(
            '/collections/%s/documents?%s',
            $collection,
            $query,
        );

        $data = $this->send('POST', $path, $payload);

        return $this->toDocument($data, $document);
    }

    /**
     * @template T of DocumentObject
     *
     * @param  array<string, mixed>  $payload
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function upsert(
        string $collection,
        array $payload,
        ?string $document = null,
    ): DocumentObject {
        return $this->index($collection, $payload, $document, 'upsert');
    }

    /**
     * @param  array<int, array<mixed>>  $payloads
     * @param  'create'|'upsert'|'update'|'emplace'  $action
     * @param  'coerce_or_reject'|'coerce_or_drop'|'drop'|'reject'  $dirty_values
     * @return array<int, ImportedDocument>
     */
    public function import(
        string $collection,
        array $payloads,
        string $action = 'create',
        bool $return_id = false,
        bool $return_doc = false,
        string $dirty_values = 'coerce_or_reject',
        int $batch_size = 40,
    ): array {
        $query = http_build_query([
            'action' => $action,
            'return_id' => $return_id ? 'true' : 'false',
            'return_doc' => $return_doc ? 'true' : 'false',
            'dirty_values' => $dirty_values,
            'batch_size' => $batch_size,
        ]);

        $path = sprintf(
            '/collections/%s/documents/import?%s',
            $collection,
            $query,
        );

        $data = $this->send('POST', $path, $payloads, true, true);

        return array_map(
            fn (stdClass $datum) => ImportedDocument::from($datum),
            $data,
        );
    }

    /**
     * @template T of DocumentObject
     *
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function retrieve(
        string $collection,
        string $id,
        ?string $document = null,
    ): DocumentObject {
        $path = sprintf(
            '/collections/%s/documents/%s',
            $collection,
            $id,
        );

        $data = $this->send('GET', $path);

        return $this->toDocument($data, $document);
    }

    /**
     * @template T of DocumentObject
     *
     * @param  array<string, mixed>  $payload
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function update(
        string $collection,
        string $id,
        array $payload,
        ?string $document = null,
    ): DocumentObject {
        $path = sprintf(
            '/collections/%s/documents/%s',
            $collection,
            $id,
        );

        $data = $this->send('PATCH', $path, $payload);

        return $this->toDocument($data, $document);
    }

    /**
     * @param  array<mixed>  $payload
     * @return int The number of total updated documents.
     */
    public function updateByQuery(
        string $collection,
        string $filter_by,
        array $payload,
    ): int {
        $query = http_build_query([
            'filter_by' => $filter_by,
        ]);

        $path = sprintf(
            '/collections/%s/documents?%s',
            $collection,
            $query,
        );

        $data = $this->send('PATCH', $path, $payload);

        return $data->num_updated;
    }

    /**
     * @template T of DocumentObject
     *
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function delete(
        string $collection,
        string $id,
        ?string $document = null,
    ): DocumentObject {
        $path = sprintf(
            '/collections/%s/documents/%s',
            $collection,
            $id,
        );

        $data = $this->send('DELETE', $path);

        return $this->toDocument($data, $document);
    }

    /**
     * @return int The number of total deleted documents.
     */
    public function deleteByQuery(
        string $collection,
        string $filter_by,
        int $batch_size = 100,
    ): int {
        $query = http_build_query([
            'filter_by' => $filter_by,
            'batch_size' => $batch_size,
        ]);

        $path = sprintf(
            '/collections/%s/documents?%s',
            $collection,
            $query,
        );

        $data = $this->send('DELETE', $path);

        return $data->num_deleted;
    }

    /**
     * @template T of DocumentObject
     *
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? array<int, T> : array<int, GenericDocument>)
     */
    public function export(
        string $collection,
        string $filter_by = '',
        string $include_fields = '',
        string $exclude_fields = '',
        ?string $document = null,
    ): array {
        $query = http_build_query(
            array_filter(
                compact('filter_by', 'include_fields', 'exclude_fields'),
            ),
        );

        $path = sprintf(
            '/collections/%s/documents/export?%s',
            $collection,
            $query,
        );

        $data = $this->send(
            'GET',
            $path,
            expectArray: true,
            ndjson: true,
        );

        return array_map(
            fn (stdClass $datum) => $this->toDocument($datum, $document),
            $data,
        );
    }

    /**
     * @template T of DocumentObject
     *
     * @param  class-string<T>|null  $document
     * @return ($document is class-string<T> ? T : GenericDocument)
     */
    public function toDocument(
        stdClass $data,
        ?string $document = null,
    ): DocumentObject {
        if (
            $document === null ||
            ! is_subclass_of($document, DocumentObject::class)
        ) {
            return GenericDocument::from($data);
        }

        return new $document($data);
    }
}
