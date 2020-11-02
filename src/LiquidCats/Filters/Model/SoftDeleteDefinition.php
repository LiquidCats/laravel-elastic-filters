<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait SoftDeleteDefinition.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
trait SoftDeleteDefinition
{
    /**
     * Determine if the given model uses soft deletes.
     */
    public static function usesSoftDelete(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }
}
