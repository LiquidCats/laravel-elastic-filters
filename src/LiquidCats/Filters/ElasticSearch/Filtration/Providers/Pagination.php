<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Enum\Slugs;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class Pagination.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Pagination implements ProviderContract
{
    protected string $page;
    protected string $perPage;

    /**
     * Pagination constructor.
     */
    private function __construct(string $page = Slugs::PAGE, string $perPage = Slugs::PER_PAGE)
    {
        $this->page = $page;
        $this->perPage = $perPage;
    }

    public static function make(string $page = Slugs::PAGE, string $perPage = Slugs::PER_PAGE): self
    {
        return new static(...func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $perPage = $filters[$this->perPage] ?? 15;
        $page = $filters[$this->page] ?? 1;
        $page = ($page - 1) * $perPage;
        $builder->from((int) $page);
        $builder->take((int) $perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function slug(): string
    {
        return 'pagination';
    }
}
