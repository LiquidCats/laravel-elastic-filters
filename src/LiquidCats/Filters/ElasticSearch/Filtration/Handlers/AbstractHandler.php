<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Handlers;

use LiquidCats\Filters\Contracts\Filtration\HandlerContract;
use LiquidCats\Filters\Contracts\Filtration\ProviderContract;

/**
 * Class AbstractHandler.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
abstract class AbstractHandler implements HandlerContract
{
    /** @var ProviderContract[] */
    protected static array $providers = [];

    /**
     * {@inheritDoc}
     */
    public static function register(ProviderContract $provider): void
    {
        if (!array_key_exists($provider->slug(), static::$providers)) {
            static::$providers[$provider->slug()] = $provider;
        }
    }
}
