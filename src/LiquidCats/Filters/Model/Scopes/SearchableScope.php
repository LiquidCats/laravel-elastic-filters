<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Model\Scopes;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SearchableScope.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class SearchableScope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('searchable', static function (Builder $builder, $chunk = null) {
            $builder->chunk($chunk ?: 500, static function (Collection $models) {
                $models->filter
                    ->shouldBeSearchable()
                    ->searchable()
                ;
            });
        });

        $builder->macro('unsearchable', static function (Builder $builder, $chunk = null) {
            $builder->chunk($chunk ?: 500, static function (Collection $models) {
                $models->unsearchable();
            });
        });
    }
}
