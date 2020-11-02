<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration;

use Illuminate\Http\Request;
use LiquidCats\Filters\Contracts\EngineContract;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\Contracts\Filtration\HandlerContract;

/**
 * Class RequestFilter.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class RequestFilter extends AbstractFilter
{
    public function __construct(BuilderContract $builder, EngineContract $engine, HandlerContract $handler, Request $request)
    {
        parent::__construct($builder, $engine, $handler, $request->all());
    }
}
