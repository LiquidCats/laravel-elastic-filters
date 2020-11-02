<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Test\LiquidCats\Filters\AbstractTestCase;
use LiquidCats\Filters\Model\SoftDeleteDefinition;

/**
 * Class SoftDeleteDefinitionTest.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @internal
 * @coversNothing
 */
class SoftDeleteDefinitionTest extends AbstractTestCase
{
    use SoftDeleteDefinition
        ;

    /** @test */
    public function itCanCheckIfClassUsesSoftDeleteTrait(): void
    {
        $class = new class() extends Model {
            use SoftDeletes;
        };
        self::assertTrue(static::usesSoftDelete($class));

        $class = new class() extends Model {
            use SoftDeletes;
        };
        self::assertTrue(static::usesSoftDelete($class));
    }
}
