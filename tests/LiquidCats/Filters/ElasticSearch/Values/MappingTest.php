<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Test\LiquidCats\Filters\AbstractTestCase;
use LiquidCats\Filters\Contracts\MappingContract;

/**
 * Class MappingTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class MappingTest extends AbstractTestCase
{
    /** @test */
    public function itCanCorrectlyCreateMappingObject(): void
    {
        /** @var MappingContract $builder */
        $builder = $this->app[MappingContract::class];

        $builder->addInteger('id');
        $builder->addBoolean('boolean');
        $builder->addEmailField('email');

        $builder->addEmailField('email2');
        $builder->forget('email2');

        $mapping = $builder->toArray();

        self::assertArrayHasKey('properties', $mapping);
        self::assertArrayNotHasKey('email2', $mapping['properties']);

        self::assertArrayHasKey('id', $mapping['properties']);
        self::assertEquals('integer', $mapping['properties']['id']['type']);

        self::assertArrayHasKey('boolean', $mapping['properties']);
        self::assertEquals('boolean', $mapping['properties']['boolean']['type']);

        self::assertArrayHasKey('email', $mapping['properties']);
        self::assertEquals('string', $mapping['properties']['email']['type']);
        self::assertEquals('email', $mapping['properties']['email']['analyzer']);
    }
}
