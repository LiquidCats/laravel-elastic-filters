<?php

declare(strict_types=1);

namespace Test\LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LiquidCats\Filters\Model\SoftDeleteDefinition;
use Test\LiquidCats\Filters\AbstractTestCase;

/**
 * Class SoftDeleteDefinitionTest
 * @package Test\LiquidCats\Filters\ElasticSearch\Values
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class SoftDeleteDefinitionTest extends AbstractTestCase
{
    use SoftDeleteDefinition
        ;
    /** @test */
    public function it_can_check_if_class_uses_soft_delete_trait(): void
    {
        $class = new class extends Model {
            use SoftDeletes;
        };
        self::assertTrue(static::usesSoftDelete($class));

        $class = new class extends Model {
            use SoftDeletes;
        };
        self::assertTrue(static::usesSoftDelete($class));
    }
}