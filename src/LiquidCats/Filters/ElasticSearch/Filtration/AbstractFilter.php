<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration;

use BadMethodCallException;
use Illuminate\Support\Collection;
use LiquidCats\Filters\Enum\Slugs;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use LiquidCats\Filters\AbstractResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Contracts\EngineContract;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\FilterContract;
use LiquidCats\Filters\Contracts\Filtration\HandlerContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class AbstractFilter.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @method FilterContract query(string $query)
 * @method FilterContract where(...$args)
 * @method FilterContract whereIn(string $field, array $value)
 * @method FilterContract whereNotIn(string $field, array $value)
 * @method FilterContract whereBetween(string $field, array $value)
 * @method FilterContract whereNotBetween(string $field, array $value)
 * @method FilterContract whereExists(string $field)
 * @method FilterContract whereNotExists(string $field)
 * @method FilterContract orderBy(string $field, string $direction = 'asc')
 * @method FilterContract take(?int $limit)
 * @method FilterContract from(?int $from)
 * @method FilterContract whereRegexp($field, $value, $flags = 'ALL')
 * @method FilterContract whereGeoDistance($field, $value, $distance)
 * @method FilterContract whereGeoBoundingBox($field, array $value)
 * @method FilterContract whereGeoPolygon($field, array $points)
 * @method FilterContract whereGeoShape($field, array $shape)
 * @method FilterContract with($relations)
 * @method FilterContract collapse(string $field)
 * @method FilterContract select($fields)
 * @method FilterContract when($value, $callback, $default = null)
 * @method FilterContract tap($callback)
 */
abstract class AbstractFilter implements FilterContract
{
    protected BuilderContract $builder;
    protected EngineContract $engine;
    protected HandlerContract $handler;
    protected array $filters;
    protected ?IndexContract $configurator = null;

    /**
     * AbstractFilter constructor.
     */
    public function __construct(BuilderContract $builder, EngineContract $engine, HandlerContract $handler, array $filters = [])
    {
        $this->handler = $handler;
        $this->filters = $filters;
        $this->builder = $builder;
        $this->engine = $engine;
    }

    /**
     * Delegate all of the calls to builder.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        if (method_exists($this->builder, $name)) {
            $this->builder->{$name}(...$arguments);

            return $this;
        }

        throw new BadMethodCallException(sprintf('[%s::%s] method does not exist', self::class, $name));
    }

    public function apply(IndexContract $configurator): FilterContract
    {
        $this->configurator = $configurator;
        $this->handler->handle($this->builder, $this->filters);

        return $this;
    }

    public function get(): AbstractResponse
    {
        return $this->engine->search($this->configurator, $this->builder);
    }

    public function count(): int
    {
        return $this->engine->count($this->configurator, $this->builder);
    }

    public function paginate(int $perPage = 15, string $pageName = Slugs::PAGE, int $page = 1): LengthAwarePaginator
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $path = Paginator::resolveCurrentPath();

        $response = $this->get();

        return $this->paginator(
            $response->data(),
            $response->total(),
            $perPage,
            $page,
            compact('path', 'pageName')
        );
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @throws BindingResolutionException
     */
    protected function paginator(Collection $items, int $total, int $perPage, int $currentPage, array $options): LengthAwarePaginator
    {
        return Container::getInstance()
            ->makeWith(
                LengthAwarePaginator::class,
                compact('items', 'total', 'perPage', 'currentPage', 'options')
            )
        ;
    }
}
