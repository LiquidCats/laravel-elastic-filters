<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters;

use Orchestra\Testbench\TestCase;
use LiquidCats\Filters\FiltersServiceProvider;

/**
 * Class AbstractTestCase.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
abstract class AbstractTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            FiltersServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('services.search.hosts', ['filters-search:9300']);
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
