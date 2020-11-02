<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch;

use Illuminate\Support\Arr;

/**
 * Class Utils.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Utils
{
    public static function mapIds(array $results): array
    {
        return array_values(array_column(self::getData($results), '_id'));
    }

    public static function getTotalCount(array $results): int
    {
        return (int) Arr::get($results, 'hits.total', 0);
    }

    public static function getColumns(array $results): array
    {
        return Arr::get($results, '_payload.body._source', []);
    }

    public static function getData(array $results): array
    {
        return Arr::get($results, 'hits.hits', []);
    }
}
