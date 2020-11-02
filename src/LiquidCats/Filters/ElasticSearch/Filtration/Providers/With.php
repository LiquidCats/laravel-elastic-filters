<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Enum\Slugs;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits\Defaults;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits\Allowance;

/**
 * Class With.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class With implements ProviderContract
{
    use Allowance;
    use Defaults;

    protected string $slug;

    /**
     * With constructor.
     */
    private function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public static function make(string $slug = Slugs::WITH): self
    {
        return new static($slug);
    }

    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $value = $filters[$this->slug] ?? '';
        $static = $this->hasDefault() ? $this->getDefault() : [];
        $dynamic = explode(',', $value);
        $relations = array_merge($static, $dynamic);
        $allowed = !$this->isAllAllowed()
            ? array_filter($relations, [$this, 'isAllowed'])
            : $relations;

        $builder->with($allowed);
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
