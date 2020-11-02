<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Values;

/**
 * Class Aggregate.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Aggregate
{
    /** @var AggregateTerm[] */
    protected array $aggregate = [];

    private function __construct()
    {
    }

    public static function make(): self
    {
        return new static();
    }

    public function add(string $name): AggregateTerm
    {
        $term = AggregateTerm::make($name);
        $this->aggregate[] = $term;

        return $term;
    }

    public function toArray(): array
    {
        $aggs = [];

        foreach ($this->aggregate as $item) {
            $aggs[] = $item->toArray();
        }

        return array_merge([], ...$aggs);
    }
}
