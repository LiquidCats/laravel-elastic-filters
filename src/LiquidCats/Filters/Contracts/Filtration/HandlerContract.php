<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts\Filtration;

use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Interface HandlerContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface HandlerContract
{
    /**
     * Register filter provider.
     *
     * @return mixed
     */
    public static function register(ProviderContract $provider);

    public function handle(BuilderContract $builder, array $filters = []): BuilderContract;
}
