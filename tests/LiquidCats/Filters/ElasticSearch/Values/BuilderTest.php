<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Elasticsearch\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Container\Container;
use LiquidCats\Filters\ElasticSearch\Builder;
use Test\LiquidCats\Filters\AbstractTestCase;
use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Class BuildersTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class BuilderTest extends AbstractTestCase
{
    use WithFaker;

    /** @test */
    public function itCanGetInstanceOfBuilder(): void
    {
        $builder = $this->app[BuilderContract::class];

        self::assertInstanceOf(BuilderContract::class, $builder);
        self::assertInstanceOf(Builder::class, $builder);
    }

    /** @test */
    public function itCanBuildBodyForQuery(): void
    {
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $builder->query($expected = Str::random());

        $query = $builder->query;

        self::assertEquals($expected, $query);
    }

    /** @test */
    public function itCanBuildBodyForWhere(): void
    {
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.term');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertEquals($value, $term[$column]);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '!=', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must_not.0.term');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertEquals($value, $term[$column]);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '<>', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must_not.0.term');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertEquals($value, $term[$column]);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '>', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('gt', $term[$column]);
        self::assertEquals($value, $term[$column]['gt']);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '<', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('lt', $term[$column]);
        self::assertEquals($value, $term[$column]['lt']);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '>=', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('gte', $term[$column]);
        self::assertEquals($value, $term[$column]['gte']);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->randomDigit;

        $builder->where($column, '<=', $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('lte', $term[$column]);
        self::assertEquals($value, $term[$column]['lte']);
    }

    /** @test */
    public function itCanBuildBodyForWhereIn(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [$this->faker->randomDigit, $this->faker->randomDigit, $this->faker->randomDigit];

        $builder->whereIn($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.terms');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertIsArray($term[$column]);
        self::assertEquals($value, $term[$column]);
    }

    /** @test */
    public function itCanBuildBodyForWhereNotIn(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [$this->faker->randomDigit, $this->faker->randomDigit, $this->faker->randomDigit];

        $builder->whereNotIn($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must_not.0.terms');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertIsArray($term[$column]);
        self::assertEquals($value, $term[$column]);
    }

    /** @test */
    public function itCanBuildBodyForWhereBetween(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value0 = $this->faker->numberBetween(1, 5);
        $value1 = $this->faker->numberBetween(6, 10);

        $builder->whereBetween($column, [$value0, $value1]);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('lte', $term[$column]);
        self::assertEquals($value1, $term[$column]['lte']);
        self::assertArrayHasKey('gte', $term[$column]);
        self::assertEquals($value0, $term[$column]['gte']);
    }

    /** @test */
    public function itCanBuildBodyForWhereNotBetween(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value0 = $this->faker->numberBetween(1, 5);
        $value1 = $this->faker->numberBetween(6, 10);

        $builder->whereNotBetween($column, [$value0, $value1]);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must_not.0.range');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertArrayHasKey('lte', $term[$column]);
        self::assertEquals($value1, $term[$column]['lte']);
        self::assertArrayHasKey('gte', $term[$column]);
        self::assertEquals($value0, $term[$column]['gte']);
    }

    /** @test */
    public function itCanBuildBodyForWhereExists(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;

        $builder->whereExists($column);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.exists');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey('field', $term);
        self::assertEquals($column, $term['field']);
    }

    /** @test */
    public function itCanBuildBodyForWhereNotExists(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;

        $builder->whereNotExists($column);

        $body = $builder->wheres;
        $term = Arr::get($body, 'must_not.0.exists');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey('field', $term);
        self::assertEquals($column, $term['field']);
    }

    /** @test */
    public function itCanBuildBodyForOrderBy(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;

        $builder->orderBy($column);

        $body = $builder->orders[0];

        $key = sprintf('%s', $column);

        $term = Arr::get($body, $key);

        self::assertNotNull($term);
        self::assertIsString($term);
        self::assertEquals('asc', $term);
    }

    /** @test */
    public function itCanBuildBodyForTake(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $size = $this->faker->randomDigit;

        $builder->take($size);

        $term = $builder->limit;

        self::assertNotNull($term);
        self::assertIsNumeric($term);
        self::assertEquals($size, $term);
    }

    /** @test */
    public function itCanBuildBodyForFrom(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $page = $this->faker->randomDigit;

        $builder->from($page);

        $term = $builder->offset;

        self::assertNotNull($term);
        self::assertIsNumeric($term);
        self::assertEquals($page, $term);
    }

    /** @test */
    public function itCanBuildBodyForWhereRegexp(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->regexify(Str::random());
        $flags = 'G';

        $builder->whereRegexp($column, $value, $flags);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.regexp');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);

        self::assertIsArray($term[$column]);
        self::assertArrayHasKey('value', $term[$column]);
        self::assertArrayHasKey('flags', $term[$column]);

        self::assertEquals($value, $term[$column]['value']);
        self::assertEquals($flags, $term[$column]['flags']);
    }

    /** @test */
    public function itCanBuildBodyForWhereGeoDistance(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [$this->faker->randomDigit, $this->faker->randomDigit];
        $distance = $this->faker->randomDigit.'km';

        $builder->whereGeoDistance($column, $value, $distance);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.geo_distance');

        self::assertNotNull($term);
        self::assertIsArray($term);

        self::assertArrayHasKey('distance', $term);
        self::assertArrayHasKey($column, $term);

        self::assertEquals($distance, $term['distance']);
        self::assertEquals($value, $term[$column]);
    }

    /** @test */
    public function itCanBuildBodyForWhereGeoBoundingBox(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [
            'top_left' => [
                'lat' => 40.73,
                'lon' => -74.1,
            ],
            'bottom_right' => [
                'lat' => 40.01,
                'lon' => -71.12,
            ],
        ];

        $builder->whereGeoBoundingBox($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.geo_bounding_box');

        self::assertNotNull($term);
        self::assertIsArray($term);

        self::assertArrayHasKey($column, $term);

        self::assertEquals($value, $term[$column]);
    }

    /** @test */
    public function itCanBuildBodyForWhereGeoPolygon(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [
            [$this->faker->randomFloat(), $this->faker->randomFloat()],
            [$this->faker->randomFloat(), $this->faker->randomFloat()],
            [$this->faker->randomFloat(), $this->faker->randomFloat()],
            [$this->faker->randomFloat(), $this->faker->randomFloat()],
        ];

        $builder->whereGeoPolygon($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.geo_polygon');

        self::assertNotNull($term);
        self::assertIsArray($term);

        self::assertArrayHasKey($column, $term);

        self::assertArrayHasKey('points', $term[$column]);
        self::assertEquals($value, $term[$column]['points']);
    }

    /** @test */
    public function itCanBuildBodyForWhereGeoShape(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = [
            'type' => 'circle',
            'radius' => $this->faker->randomDigit.'km',
            'coordinates' => [$this->faker->randomFloat(), $this->faker->randomFloat()],
        ];

        $builder->whereGeoShape($column, $value);

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.geo_shape');

        self::assertNotNull($term);
        self::assertIsArray($term);

        self::assertArrayHasKey($column, $term);

        self::assertArrayHasKey('shape', $term[$column]);
        self::assertEquals($value, $term[$column]['shape']);
    }

    /** @test */
    public function itCanBuildBodyForWith(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];
        $with = implode(',', [$this->faker->word, $this->faker->word, $this->faker->word]);

        $builder->with($with);

        self::assertTrue(property_exists($builder, 'with'));
        self::assertEquals(explode(',', $with), $builder->with);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $with = [$this->faker->word, $this->faker->word, $this->faker->word];
        $builder->with($with);

        self::assertTrue(property_exists($builder, 'with'));
        self::assertEquals($with, $builder->with);
    }

    /** @test */
    public function itCanBuildBodyForCollapse(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;

        $builder->collapse($column);

        $term = $builder->collapse;

        self::assertNotNull($term);
        self::assertIsString($term);
        self::assertEquals($column, $term);
    }

    /** @test */
    public function itCanBuildBodyForSelect(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;

        $builder->select($column);

        $term = $builder->select;

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertContains($column, $term);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column1 = $this->faker->word;
        $column2 = $this->faker->word;
        $column3 = $this->faker->word;

        $builder->select([$column1, $column2, $column3]);

        $term = $builder->select;

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertContains($column1, $term);
        self::assertContains($column2, $term);
        self::assertContains($column3, $term);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column1 = $this->faker->word;
        $column2 = $this->faker->word;
        $column3 = $this->faker->word;
        $column4 = $this->faker->word;

        $builder->select([$column1, $column2, $column3]);
        $builder->select($column4);

        $term = $builder->select;

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertContains($column1, $term);
        self::assertContains($column2, $term);
        self::assertContains($column3, $term);
        self::assertContains($column4, $term);
    }

    /** @test */
    public function itCanBuildBodyConditionally(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->word;

        $builder->when(true, static function (BuilderContract $builder) use ($column, $value) {
            $builder->where($column, $value);
        });

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.term');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertEquals($value, $term[$column]);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->word;

        $builder->tap(static function (BuilderContract $builder) use ($column, $value) {
            $builder->where($column, $value);
        });

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.term');

        self::assertNotNull($term);
        self::assertIsArray($term);
        self::assertArrayHasKey($column, $term);
        self::assertEquals($value, $term[$column]);
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $column = $this->faker->word;
        $value = $this->faker->word;

        $builder->when(false, static function (BuilderContract $builder) use ($column, $value) {
            $builder->where($column, $value);
        });

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.term');

        self::assertNull($term);
    }

    /** @test */
    public function itCanBuildBodyWithTrashed(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];

        $builder->where('__soft_deleted', 0);
        $builder->withTrashed();

        $body = $builder->wheres;

        $term = Arr::get($body, 'must');

        self::assertNotNull($term);
        self::assertEmpty($term);
    }

    /** @test */
    public function itCanBuildBodyOnlyTrashed(): void
    {
        // =======
        /** @var BuilderContract $builder */
        $builder = $this->app[BuilderContract::class];
        $builder->where('__soft_deleted', 0);
        $builder->onlyTrashed();

        $body = $builder->wheres;

        $term = Arr::get($body, 'must.0.term.__soft_deleted');

        self::assertNotNull($term);
        self::assertIsNumeric($term);
        self::assertEquals(1, $term);
    }

    protected function fakeData($size = 3): Collection
    {
        $result = collect();
        do {
            $result->push([
                '_index' => 'twitter',
                '_type' => 'twitter',
                '_id' => $this->faker->numberBetween(1000, 9999),
                '_score' => $this->faker->randomFloat(),
                '_source' => [
                    'user' => $this->faker->word,
                    'message' => $this->faker->words(),
                    'date' => $this->faker->dateTime(),
                    'likes' => $this->faker->randomDigit,
                ],
            ]);
            --$size;
        } while ($size > 0);

        return $result;
    }

    protected function fakeElastic(Collection $items): void
    {
        $shards = collect()
            ->put('total', $items->count())
            ->put('successful', $items->count())
            ->put('skipped', 0)
            ->put('failed', 0)
        ;

        $return = collect()
            ->put('took', 1)
            ->put('timed_out', false)
            ->put('_shards', $shards->toArray())
            ->put('hits', [
                'total' => [
                    'value' => $items->count(),
                    'relation' => 'eq',
                ],
                'max_score' => 1.3862944,
                'hits' => $items->toArray(),
            ])
        ;

        $this->app->bind(Client::class, static function (Container $app) use ($return) {
            $mock = \Mockery::mock(Client::class);
            $mock->shouldReceive('search')
                ->withAnyArgs()
                ->andReturn($return->toArray())
            ;

            return $mock;
        });
    }
}
