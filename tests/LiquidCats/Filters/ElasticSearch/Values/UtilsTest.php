<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Support\Arr;
use LiquidCats\Filters\ElasticSearch\Utils;
use Test\LiquidCats\Filters\AbstractTestCase;

/**
 * Class UtilsTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class UtilsTest extends AbstractTestCase
{
    /** @test */
    public function itCanExecuteMapIds(): void
    {
        $data = [];
        Arr::set($data, 'hits.hits', $items = [
            ['_id' => 1],
            ['_id' => 2],
            ['_id' => 3],
        ]);
        $ids = Utils::mapIds($data);
        self::assertEquals(array_column($items, '_id'), $ids);
    }

    /** @test */
    public function itCanExecuteGetTotalCount(): void
    {
        $data = [];
        Arr::set($data, 'hits.total', $total = 10);

        $fromIndex = Utils::getTotalCount($data);

        self::assertEquals($total, $fromIndex);
    }

    /** @test */
    public function itCanExecuteGetColumns(): void
    {
        $data = [];
        Arr::set($data, '_payload.body._source', $columns = ['c1', 'c2']);
        $fromIndex = Utils::getColumns($data);

        self::assertEquals($columns, $fromIndex);
    }

    /** @test */
    public function itCanExecuteGetData(): void
    {
        $data = [];
        Arr::set($data, 'hits.hits', $items = [
            ['_id' => 1],
            ['_id' => 2],
            ['_id' => 3],
        ]);
        $fromIndex = Utils::getData($data);

        self::assertEquals($items, $fromIndex);
    }
}
