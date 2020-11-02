<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class DefaultProvider.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class DefaultProvider implements ProviderContract
{
    protected string $field;

    /**
     * BooleanProvider constructor.
     */
    public function __construct(string $field)
    {
        $this->field = $field;
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
        if (null !== $value) {
            $builder->where($this->field, $value);
        }
    }

    public function slug(): string
    {
        return 'filter_'.$this->field;
    }
}
