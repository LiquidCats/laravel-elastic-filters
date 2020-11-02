<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Enum\Slugs;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class Query.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Query implements ProviderContract
{
    protected string $slug;

    /**
     * Query constructor.
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @param string $slug
     *
     * @return static
     */
    public static function make($slug = Slugs::QUERY): self
    {
        return new static($slug);
    }

    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $search = $filters[$this->slug] ?? '*';
        $builder->query($search);
    }

    /**
     * {@inheritDoc}
     */
    public function slug(): string
    {
        return $this->slug;
    }
}
