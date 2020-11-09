<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Model;

use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Jobs\MakeSearchable;
use LiquidCats\Filters\Jobs\MakeUnsearchable;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Model\Scopes\SearchableScope;
use LiquidCats\Filters\Model\Observers\SearchableObserver;
use LiquidCats\Filters\Contracts\Filtration\FilterContract;

/**
 * Trait Searchable.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 * @mixin Model
 */
trait Searchable
{
    public static function bootSearchableTrait(): void
    {
        static::addGlobalScope(new SearchableScope());
        static::observe(new SearchableObserver());
        static::registerSearchableMacros();
    }

    public function search(): FilterContract
    {
        /** @var FilterContract $filter */
        $filter = Container::getInstance()
            ->make(FilterContract::class)
        ;

        return $filter->apply(self::getConfigurator());
    }

    public function queueMakeSearchable(Collection $models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $model = $models->first();
        MakeSearchable::dispatch($model::getConfigurator(), $models);
    }

    public function queueRemoveFromSearch(Collection $models): void
    {
        if ($models->isEmpty()) {
            return;
        }

        $model = $models->first();
        MakeUnsearchable::dispatch($model::getConfiguratorStack(), $models);
    }

    /**
     * Make the given model instance searchable.
     */
    public function searchable(): void
    {
        $this->newCollection([$this])->searchable();
    }

    /**
     * Remove the given model instance from the search index.
     */
    public function unsearchable(): void
    {
        $this->newCollection([$this])->unsearchable();
    }

    public function shouldBeSearchable(): bool
    {
        return true;
    }

    public static function getConfigurator(): IndexContract
    {
        if (!isset(static::$indexConfigurator) || empty(static::$indexConfigurator)) {
            throw new RuntimeException(sprintf(
                'An index configurator for the %s model is not specified.',
                __CLASS__
            ));
        }

        return Container::getInstance()
            ->make(static::$indexConfigurator)
        ;
    }

    /**
     * Register the searchable macros.
     */
    protected static function registerSearchableMacros(): void
    {
        $self = new static();

        Collection::macro('searchable', function () use ($self) {
            $self->queueMakeSearchable($this);
        });

        Collection::macro('unsearchable', function () use ($self) {
            $self->queueRemoveFromSearch($this);
        });
    }
}
