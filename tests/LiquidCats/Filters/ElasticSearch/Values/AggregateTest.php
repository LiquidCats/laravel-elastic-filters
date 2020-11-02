<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Test\LiquidCats\Filters\AbstractTestCase;
use LiquidCats\Filters\ElasticSearch\Values\Aggregate;

/**
 * Class AggregateTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class AggregateTest extends AbstractTestCase
{
    /** @test */
    public function itCanCreateAggregate(): void
    {
        $aggregate = Aggregate::make();
        $term1 = $aggregate->add('test1');
        $term1->minDate('test_date_1');

        $term2 = $aggregate->add('test2');
        $term2->maxDate('test_date_2');

        $term3 = $term2->addAggregate('test3');
        $term3->terms('test_field');
        $term4 = $term3->addAggregate('test4');
        $term4->dateHistogram('test_date_3');

        $result = $aggregate->toArray();

        self::assertNotEmpty($result);

        self::assertArrayHasKey('test1', $result);
        self::assertArrayHasKey('min', $result['test1']);
        self::assertArrayHasKey('field', $result['test1']['min']);
        self::assertArrayHasKey('format', $result['test1']['min']);
        self::assertEquals('test_date_1', $result['test1']['min']['field']);
        self::assertEquals('yyyy-MM-dd', $result['test1']['min']['format']);

        self::assertArrayHasKey('test2', $result);
        self::assertArrayHasKey('max', $result['test2']);
        self::assertArrayHasKey('aggs', $result['test2']);
        self::assertArrayHasKey('field', $result['test2']['max']);
        self::assertArrayHasKey('format', $result['test2']['max']);
        self::assertEquals('test_date_2', $result['test2']['max']['field']);
        self::assertEquals('yyyy-MM-dd', $result['test2']['max']['format']);

        self::assertArrayHasKey('test3', $result['test2']['aggs']);
        self::assertArrayHasKey('terms', $result['test2']['aggs']['test3']);
        self::assertArrayHasKey('field', $result['test2']['aggs']['test3']['terms']);
        self::assertEquals('test_field', $result['test2']['aggs']['test3']['terms']['field']);

        self::assertArrayHasKey('aggs', $result['test2']['aggs']['test3']);
        self::assertArrayHasKey('test4', $result['test2']['aggs']['test3']['aggs']);
        self::assertArrayHasKey('date_histogram', $result['test2']['aggs']['test3']['aggs']['test4']);
        self::assertArrayHasKey('field', $result['test2']['aggs']['test3']['aggs']['test4']['date_histogram']);
        self::assertArrayHasKey('interval', $result['test2']['aggs']['test3']['aggs']['test4']['date_histogram']);
        self::assertEquals('test_date_3', $result['test2']['aggs']['test3']['aggs']['test4']['date_histogram']['field']);
        self::assertEquals('day', $result['test2']['aggs']['test3']['aggs']['test4']['date_histogram']['interval']);
    }
}
