<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Handlers;

use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Class DefaultHandler.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Handler extends AbstractHandler
{
    public function handle(BuilderContract $builder, array $filters = []): BuilderContract
    {
        foreach (static::$providers as $provider) {
            $provider->provide($builder, $filters);
        }

        return $builder;
    }
}
