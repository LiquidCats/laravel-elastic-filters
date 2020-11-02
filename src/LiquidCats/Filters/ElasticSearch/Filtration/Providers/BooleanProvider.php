<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class BooleanProvider.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class BooleanProvider implements ProviderContract
{
    protected string $field;

    /**
     * BooleanProvider constructor.
     */
    public function __construct(string $slug)
    {
        $this->field = $slug;
    }

    public static function make(string $field): self
    {
        return new static($field);
    }

    /**
     * {@inheritDoc}
     */
    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $value = $filters[$this->slug()] ?? null;
        if (null === $value) {
            return;
        }
        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (null === $bool) {
            return;
        }

        $builder->where($this->field, '=', $bool);
    }

    public function slug(): string
    {
        return 'filter.'.$this->field;
    }
}
