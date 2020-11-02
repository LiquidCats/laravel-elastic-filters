<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Values;

/**
 * Class AggregateTerm.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class AggregateTerm
{
    protected string $name;
    protected array $term;
    protected Aggregate $aggregates;

    /**
     * AggregateTerm constructor.
     */
    private function __construct(string $name)
    {
        $this->aggregates = Aggregate::make();
        $this->name = $name;
    }

    public static function make(string $name): self
    {
        return new static(...func_get_args());
    }

    public function minDate(string $field, string $format = 'yyyy-MM-dd'): void
    {
        $this->term = ['min' => compact('field', 'format')];
    }

    public function maxDate(string $field, string $format = 'yyyy-MM-dd'): void
    {
        $this->term = ['max' => compact('field', 'format')];
    }

    public function dateHistogram(string $field, string $interval = 'day'): void
    {
        $this->term = ['date_histogram' => compact('field', 'interval')];
    }

    public function terms(string $field): void
    {
        $this->term = ['terms' => compact('field')];
    }

    public function addAggregate(string $name): self
    {
        return $this->aggregates->add($name);
    }

    public function raw(array $term): void
    {
        $this->term = $term;
    }

    public function toArray(): array
    {
        $aggs = $this->aggregates->toArray();

        if (!empty($aggs)) {
            $this->term['aggs'] = $aggs;
        }

        return [$this->name => $this->term];
    }
}
