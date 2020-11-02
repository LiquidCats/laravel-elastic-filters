<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts\Filtration;

use LiquidCats\Filters\Enum\Slugs;
use LiquidCats\Filters\AbstractResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\ElasticSearch\Values\Response;
use LiquidCats\Filters\ElasticSearch\Filtration\RequestFilter;

/**
 * Interface FilterContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 *
 * @method RequestFilter query(string $query)
 * @method RequestFilter where(...$args)
 * @method RequestFilter whereIn(string $field, array $value)
 * @method RequestFilter whereNotIn(string $field, array $value)
 * @method RequestFilter whereBetween(string $field, array $value)
 * @method RequestFilter whereNotBetween(string $field, array $value)
 * @method RequestFilter whereExists(string $field)
 * @method RequestFilter whereNotExists(string $field)
 * @method RequestFilter orderBy(string $field, string $direction = 'asc')
 * @method RequestFilter take(?int $limit)
 * @method RequestFilter from(?int $from)
 * @method RequestFilter whereRegexp($field, $value, $flags = 'ALL')
 * @method RequestFilter whereGeoDistance($field, $value, $distance)
 * @method RequestFilter whereGeoBoundingBox($field, array $value)
 * @method RequestFilter whereGeoPolygon($field, array $points)
 * @method RequestFilter whereGeoShape($field, array $shape)
 * @method RequestFilter with($relations)
 * @method RequestFilter collapse(string $field)
 * @method RequestFilter select($fields)
 * @method RequestFilter when($value, $callback, $default = null)
 * @method RequestFilter tap($callback)
 */
interface FilterContract
{
    /**
     * Get model class and provider search and filtration.
     */
    public function apply(IndexContract $conf): FilterContract;

    /**
     * @return Response
     */
    public function get(): AbstractResponse;

    public function count(): int;

    public function paginate(int $perPage = 15, string $pageName = Slugs::PAGE, int $page = 1): LengthAwarePaginator;
}
