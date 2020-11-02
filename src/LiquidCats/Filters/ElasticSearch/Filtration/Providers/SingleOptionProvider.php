<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class SingleOptionProvider.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class SingleOptionProvider implements ProviderContract
{
    protected string $field;
    protected array $map;

    private function __construct(string $field, array $map)
    {
        $this->field = $field;
        $this->map = $map;
    }

    public static function make(string $slug, array $map = []): self
    {
        return new static($slug, $map);
    }

    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $value = $filters[$this->slug()] ?? null;
        if (null === $value) {
            return;
        }
        if (empty($value)) {
            return;
        }
        if (!array_key_exists($value, $this->map)) {
            return;
        }

        $builder->where($this->field, '=', $value);
    }

    public function slug(): string
    {
        return 'filter_'.$this->field;
    }
}
