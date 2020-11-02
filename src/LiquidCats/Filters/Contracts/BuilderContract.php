<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts;

use LiquidCats\Filters\ElasticSearch\Values\Aggregate;

/**
 * Interface BuilderContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface BuilderContract
{
    public function query(string $query): BuilderContract;

    /**
     * @param mixed ...$args
     */
    public function where(...$args): BuilderContract;

    public function whereIn(string $field, array $value): BuilderContract;

    public function whereNotIn(string $field, array $value): BuilderContract;

    public function whereBetween(string $field, array $value): BuilderContract;

    public function whereNotBetween(string $field, array $value): BuilderContract;

    public function whereExists(string $field): BuilderContract;

    public function whereNotExists(string $field): BuilderContract;

    public function orderBy(string $field, string $direction = 'asc'): BuilderContract;

    /**
     * @param null|int $limit
     */
    public function take(int $limit = 15): BuilderContract;

    public function from(?int $from): BuilderContract;

    /**
     * @param string $flags
     *
     * @return $this
     */
    public function whereRegexp(string $field, string $value, $flags = 'ALL'): self;

    /**
     * @param $value
     * @param $distance
     *
     * @return $this
     */
    public function whereGeoDistance(string $field, $value, $distance): self;

    /**
     * @return $this
     */
    public function whereGeoBoundingBox(string $field, array $value): self;

    /**
     * @return $this
     */
    public function whereGeoPolygon(string $field, array $points): self;

    /**
     * @return $this
     */
    public function whereGeoShape(string $field, array $shape): self;

    /**
     * @param $relations
     *
     * @return $this
     */
    public function with($relations): self;

    /**
     * @return $this
     */
    public function collapse(string $field): self;

    /**
     * @param $fields
     *
     * @return $this
     */
    public function select($fields): self;

    /**
     * @return $this
     */
    public function withTrashed(): self;

    /**
     * @return $this
     */
    public function onlyTrashed(): self;

    /**
     * @param $value
     * @param $callback
     * @param null $default
     *
     * @return $this
     */
    public function when($value, $callback, $default = null): self;

    /**
     * @param $callback
     *
     * @return $this
     */
    public function tap($callback): self;

    public function aggregate(Aggregate $aggr): BuilderContract;
}
