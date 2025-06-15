<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Services\Address\Domain\Address;
use App\Services\Address\Infrastructure\AddressSearchInterface;
use App\Services\Search\Exceptions\ElasticaClientException;
use App\Services\Search\Exceptions\ElasticaIndexException;
use App\Services\Search\Exceptions\ElasticaServerException;
use Elastic\Elasticsearch\ClientInterface as ElasticSearchClientInterface;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Http\Promise\Promise;

readonly class ElasticSearchService implements AddressSearchInterface
{
    public const string INDEX_NAME ='addresses';

    public function __construct(
        private ElasticSearchClientInterface $client
    ) {
    }

    /**
     * Test the connection to the Elasticsearch server.
     *
     * @throws ElasticaServerException
     */
    public function testConnection(): void
    {
        try {
            $this->client->ping();
        } catch (\Throwable $e) {
            throw new ElasticaServerException("Connection failed", 0, ...$e);
        }
    }


    /**
     * @param string $indexName
     * @return Elasticsearch|Promise
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function createIndex(string $indexName): Elasticsearch|Promise
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0
                ],
                'mappings' => [
                    'properties' => [
                        'title' => [
                            'type' => 'text'
                        ],
                        'content' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        ];

        return $this->client->indices()->create($params);
    }

    /**
     * Populate an index with the given data.
     *
     * @param array $data
     *
     * @return Elasticsearch|Promise
     *
     * @throws ElasticaServerException
     * @throws ElasticaClientException
     */
    public function populateIndex(array $data): Elasticsearch|Promise
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => $data
        ];

        try {
            return $this->client->index($params);
        } catch(MissingParameterException|ClientResponseException $e) {
            throw new ElasticaClientException($e->getMessage(), $e->getCode(), ...$e);
        } catch (ServerResponseException|NoNodeAvailableException $e) {
            throw new ElasticaServerException($e->getMessage(), $e->getCode(), ...$e);
        }
    }

    /**
     * @param string $id
     * @return void
     * @throws ElasticaClientException
     * @throws ElasticaServerException
     */
    public function delete(string $id): void
    {
        $params = [
            'index' => self::INDEX_NAME,
            '_id' => $id
        ];
        try {
            $this->client->delete($params);
        } catch(MissingParameterException|ClientResponseException $e) {
            throw new ElasticaClientException($e->getMessage(), $e->getCode(), $e);
        } catch (ServerResponseException|NoNodeAvailableException $e) {
            throw new ElasticaServerException($e->getMessage(), $e->getCode(), $e);
        }

    }
    /**
     * @throws ElasticaClientException
     */
    public function search(string ...$searchTerms): iterable
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'match' => [
                        'first_name' => implode($searchTerms)
                    ]
                ]
            ]
        ];
        try {
            $response = $this->client->search($params);
        }  catch (\Throwable $e) {
            throw new ElasticaClientException("Client Exception", 0, ...$e);
        }

        $responseArray = $response->asArray();

        $results = [];
        if (count($responseArray['hits']['hits']) > 0) {
            foreach ($responseArray['hits']['hits'] as $hit) {
                $results[] = new Address(
                    $hit['_source']['first_name'],
                    $hit['_source']['last_name'],
                    $hit['_source']['phone'],
                    $hit['_source']['email'],
                );
            }
        }
        return array_unique($results, SORT_REGULAR);

    }
}