<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Console;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\ElasticSearch\Values\Payload;
use Illuminate\Contracts\Container\BindingResolutionException;
use LiquidCats\Filters\Console\Traits\GetConfiguratorArguments;

/**
 * Class CreateIndex.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class CreateIndex extends Command
{
    use GetConfiguratorArguments;

    /**
     * @var string
     */
    protected $name = 'search:create-index';

    /**
     * @var string
     */
    protected $description = 'Create an search index';

    /**
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        /** @var Client $client */
        $client = $this->laravel->make(Client::class);
        $configurator = $this->getIndexConfigurator();
        $indices = $client->indices();
        $indices->create($this->getPayload($configurator));

        $this->info(sprintf('The [%s] index was created', get_class($configurator)));
    }

    protected function getPayload(IndexContract $configurator): array
    {
        $payload = new Payload();
        $payload->setIfNotEmpty('index', $configurator->getName());
        $payload->setIfNotEmpty('body.settings', $configurator->getSettings());
        $payload->setIfNotEmpty('body.mappings.'.$configurator->getName(), $configurator->getMapping()->toArray());

        return $payload->get();
    }
}
