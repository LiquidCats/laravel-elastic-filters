<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Model\Searchable;
use LiquidCats\Filters\ElasticSearch\Mapping;
use Test\LiquidCats\Filters\AbstractTestCase;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\MappingContract;
use LiquidCats\Filters\ElasticSearch\AbstractIndex;
use LiquidCats\Filters\Contracts\Filtration\FilterContract;
use LiquidCats\Filters\ElasticSearch\Filtration\RequestFilter;
use LiquidCats\Filters\ElasticSearch\Filtration\Handlers\AbstractHandler;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\DefaultProvider;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\MultiOptionsProvider;

/**
 * Class RequestFilterTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class RequestFilterTest extends AbstractTestCase
{
    /**
     * @test
     *
     * @throws
     */
    public function itCanCreateFilterObjectFromRequestAndForwardCallsToBuilder(): void
    {
        AbstractHandler::register(DefaultProvider::make('test1'));
        AbstractHandler::register(MultiOptionsProvider::make('test2', [0 => 'z', 1 => 'a', 2 => 'b', 'c']));

        $req = Request::capture();
        $req->query->add([
            'filter_test1' => '23',
            'filter_test2' => '1,2,3',
        ]);

        $this->app->instance(Request::class, $req);

        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];
        /** @var FilterContract $filter */
        $filter = $this->app->make(FilterContract::class, compact('builder'));

        self::assertInstanceOf(RequestFilter::class, $filter);
        $configurator = $this->getFakeModel()::getConfigurator();
        $filter->apply($configurator);

        $body = $builder->wheres;

        self::assertIsArray($body);
        self::assertNotEmpty($body);

        $must = Arr::get($body, 'must', []);
        self::assertIsArray($must);
        self::assertCount(2, $must);
        $range = $must[0];
        self::assertIsArray($range);
        self::assertArrayHasKey('test2', $range['terms']);
        self::assertIsArray($range['terms']['test2']);
        self::assertEquals(['1', '2', '3'], $range['terms']['test2']);

        $term = $must[1];
        self::assertArrayHasKey('term', $term);
        self::assertArrayHasKey('test1', $term['term']);
        self::assertEquals('23', $term['term']['test1']);
    }

    protected function getFakeModel(): Model
    {
        return new class() extends Model {
            use Searchable;

            protected static $indexConfigurator = FakeIndexConfigurator::class;
        };
    }
}

class FakeIndexConfigurator extends AbstractIndex
{
    /**
     * {@inheritDoc}
     */
    public function getMapping(): MappingContract
    {
        return new Mapping();
    }

    /**
     * {@inheritDoc}
     */
    public function mapToIndex($data): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return '';
    }
}
