<?php

declare(strict_types=1);

use LiquidCats\Filters\ElasticSearch\Filtration\Providers\With;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Query;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\OrderSort;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\Pagination;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\BooleanProvider;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\DefaultProvider;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\MultiOptionsProvider;
use LiquidCats\Filters\ElasticSearch\Filtration\Providers\SingleOptionProvider;

return [
    'providers' => [
        'handle' => [
            BooleanProvider::class,
            DefaultProvider::class,
            MultiOptionsProvider::class,
            OrderSort::class,
            Pagination::class,
            Query::class,
            SingleOptionProvider::class,
            With::class,
        ],
    ],
];
