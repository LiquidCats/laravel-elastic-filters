<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Enum\Slugs;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits\Defaults;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits\Allowance;

/**
 * Class OrderSortBuilder.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class OrderSort implements ProviderContract
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

    public static function make(string $slug = Slugs::SORT): self
    {
        return new static($slug);
    }

    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $rawSorts = $filters[$this->slug] ?? '';
        if (empty($rawSorts) && $this->hasDefault()) {
            $builder->orderBy(...$this->getDefault());

            return;
        }
        $sortArray = explode(';', $rawSorts);

        $allowAll = $this->isAllAllowed();
        foreach ($sortArray as $singleSort) {
            [$column, $direction] = $this->parseSort($singleSort);

            if ($allowAll || $this->isAllowed($column)) {
                $builder->orderBy((string) $column, $direction);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function slug(): string
    {
        return $this->slug;
    }

    protected function parseSort(string $singleSort): ?array
    {
        $parsed = explode(',', $singleSort);
        $column = $parsed[0] ?? null;

        if (null === $column) {
            return null;
        }

        $direction = Slugs::SORT_DESC === ($parsed[1] ?? null)
            ? Slugs::SORT_DESC
            : Slugs::SORT_ASC;

        return [$column, $direction];
    }
}
