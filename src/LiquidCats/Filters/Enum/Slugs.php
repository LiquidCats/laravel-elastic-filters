<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Enum;

/**
 * Interface Slugs.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface Slugs
{
    public const SORT = 'sort_by';
    public const SORT_ASC = 'asc';

    public const PAGE = 'page';
    public const SORT_DESC = 'desc';

    public const PER_PAGE = 'per_page';
    public const QUERY = 'query';
    public const WITH = 'with';

    public const NO_QUERY_SIGN = '*';
}
