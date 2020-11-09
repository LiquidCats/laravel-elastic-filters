<?php

declare(strict_types=1);

namespace LiquidCats\Filters;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Config\Repository;
use LiquidCats\Filters\Console\Import;
use Illuminate\Support\ServiceProvider;
use LiquidCats\Filters\Console\DropIndex;
use LiquidCats\Filters\Console\CreateIndex;
use LiquidCats\Filters\ElasticSearch\Engine;
use Illuminate\Contracts\Container\Container;
use LiquidCats\Filters\ElasticSearch\Builder;
use LiquidCats\Filters\ElasticSearch\Mapping;
use LiquidCats\Filters\Contracts\EngineContract;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\MappingContract;
use LiquidCats\Filters\Contracts\Filtration\FilterContract;
use LiquidCats\Filters\Contracts\Filtration\HandlerContract;
use LiquidCats\Filters\ElasticSearch\Filtration\RequestFilter;
use LiquidCats\Filters\ElasticSearch\Filtration\Handlers\Handler;
use LiquidCats\Filters\ElasticSearch\Filtration\Handlers\AbstractHandler;

/**
 * Class FiltersServiceProvider.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class FiltersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, static function (Container $app) {
            /** @var Repository $cfg */
            $cfg = $app[Repository::class];

            return ClientBuilder::create()
                ->setHosts($cfg->get('services.search.hosts', ['localhost:9300']))
                ->build()
            ;
        });

        $this->registerDependencies();
        $this->registerCommands();
    }

    public function registerDependencies(): void
    {
        $this->app->bind(MappingContract::class, Mapping::class);
        $this->app->bind(BuilderContract::class, Builder::class);

        $this->app->singleton(EngineContract::class, Engine::class);
        $this->app->singleton(HandlerContract::class, static function (Container $app) {
            /** @var Repository $cfg */
            $cfg = $app[Repository::class];
            $defaultProviders = $cfg->get('filters.providers,handle', []);

            foreach ($defaultProviders as $provider) {
                Handler::register($provider);
            }

            return new Handler();
        });
        $this->app->alias(HandlerContract::class, AbstractHandler::class);

        $this->app->bind(FilterContract::class, RequestFilter::class);
    }

    public function registerCommands(): void
    {
        $this->commands([
            CreateIndex::class,
            DropIndex::class,
            Import::class,
        ]);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../../configs/filters.php' => config_path('courier.php'),
        ]);
    }
}
