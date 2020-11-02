<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts\Filtration;

use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Interface ProviderContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface ProviderContract
{
    /**
     * Provide filter to builder.
     */
    public function provide(BuilderContract $builder, array $filters = []): void;

    /**
     * Return its slug string.
     */
    public function slug(): string;
}
