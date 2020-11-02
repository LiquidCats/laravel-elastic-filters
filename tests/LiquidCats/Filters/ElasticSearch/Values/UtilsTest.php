<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Support\Arr;
use LiquidCats\Filters\ElasticSearch\Utils;
use Test\LiquidCats\Filters\AbstractTestCase;

/**
 * Class UtilsTest
 * @package Test\LiquidCats\Filters\ElasticSearch\Values
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class UtilsTest extends AbstractTestCase
{
    /** @test */
    public function it_can_execute_mapIds(): void
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
    public function it_can_execute_getTotalCount(): void
    {
        $data = [];
        Arr::set($data, 'hits.total', $total = 10);

        $fromIndex = Utils::getTotalCount($data);

        self::assertEquals($total, $fromIndex);
    }

    /** @test */
    public function it_can_execute_getColumns(): void
    {
        $data = [];
        Arr::set($data, '_payload.body._source', $columns = ['c1', 'c2']);
        $fromIndex = Utils::getColumns($data);

        self::assertEquals($columns, $fromIndex);
    }

    /** @test */
    public function it_can_execute_getData(): void
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