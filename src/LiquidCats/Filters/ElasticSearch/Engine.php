<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LiquidCats\Filters\Contracts\IndexContract;
use Elasticsearch\Client as ElasticsearchClient;
use LiquidCats\Filters\Contracts\EngineContract;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\ElasticSearch\Values\Payload;
use LiquidCats\Filters\ElasticSearch\Values\Response;

/**
 * Class Engine.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Engine implements EngineContract
{
    protected ElasticsearchClient $client;

    /**
     * AbstractEngine constructor.
     */
    public function __construct(ElasticsearchClient $client)
    {
        $this->client = $client;
    }

    public function updateMapping(IndexContract $configurator): void
    {
        $this->client->indices()->putMapping($configurator->getMapping()->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function update(IndexContract $configurator, Collection $items): void
    {
        $payload = new Payload();

        $payload->set('index', $configurator->getName());
        $payload->set('type', $configurator->getType());
        $payload->set('refresh', true);

        foreach ($items as $item) {
            $mappedData = $configurator->mapToIndex($item);

            $actionPayload = (new Payload())
                ->set('index._id', $configurator->getId($item))
            ;

            $payload
                ->add('body', $actionPayload->get())
                ->add('body', $mappedData)
            ;
        }

        $this->client->bulk($payload->get());
    }

    public function delete(IndexContract $configurator, Collection $ids): void
    {
        $payload = new Payload();

        $payload->set('index', $configurator->getName());
        $payload->set('type', $configurator->getType());

        foreach ($ids as $id) {
            $actionPayload = (new Payload())
                ->set('delete._id', $id)
            ;

            $payload->add('body', $actionPayload->get());
        }
        $this->client->bulk($payload->get());
    }

    public function search(IndexContract $configurator, BuilderContract $builder): Response
    {
        $rawResult = $this->client->search($this->buildQueryParams($configurator, $builder));

        return new Response($configurator, $builder, $rawResult);
    }

    public function count(IndexContract $configurator, BuilderContract $builder): int
    {
        $params = $this->buildQueryParams($configurator, $builder);
        // clean up unsupported
        Arr::forget($params, 'body.size');

        $result = $this->client->count($params);

        return (int) Arr::get($result, 'count', 0);
    }

    protected function buildQueryParams(IndexContract $configurator, BuilderContract $builder)
    {
        $payload = new Payload();
        $payload->set('index', $configurator->getName());
        $payload->set('type', $configurator->getType());
        $payload->setIfNotEmpty('body._source', $builder->select);
        $payload->setIfNotEmpty('body.query.bool.filter.bool', $builder->wheres);
        $payload->setIfNotEmpty('body.collapse.field', $builder->collapse);
        $payload->setIfNotEmpty('body.aggs', $builder->aggregate);
        $payload->setIfNotEmpty('body.sort', $builder->orders);
        $payload->setIfNotNull('body.from', $builder->offset);
        $payload->setIfNotNull('body.size', $builder->limit);

        $payload->setIfNotEmpty('body.query.bool.must.query_string.query', $builder->query);

        return $payload->get();
    }
}
