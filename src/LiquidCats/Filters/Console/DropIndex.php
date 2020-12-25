<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Console;

use Elasticsearch\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use LiquidCats\Filters\Console\Traits\GetConfiguratorArguments;

/**
 * Class DropIndex.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class DropIndex extends Command
{
    use GetConfiguratorArguments;
    /**
     * @var string
     */
    protected $name = 'search:drop-index';

    /**
     * @var string
     */
    protected $description = 'Drop a search index';

    /**
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        /** @var Client $client */
        $client = $this->laravel->make(Client::class);

        $configurator = $this->getIndexConfigurator();
        $index = $configurator->getName();
        $client->indices()->delete(compact('index'));

        $this->info(sprintf('The [%s] index was deleted!', get_class($configurator)));
    }
}
