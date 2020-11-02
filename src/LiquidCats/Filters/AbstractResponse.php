<?php

declare(strict_types=1);

namespace LiquidCats\Filters;

use Illuminate\Support\Collection;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Class AbstractResponse.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
abstract class AbstractResponse
{
    protected IndexContract $configurator;
    protected BuilderContract $builder;
    protected array $raw;

    /**
     * ResponseDto constructor.
     */
    public function __construct(IndexContract $configurator, BuilderContract $builder, array $raw = [])
    {
        $this->configurator = $configurator;
        $this->builder = $builder;
        $this->raw = $raw;
    }

    abstract public function data(): Collection;

    abstract public function keys(): Collection;

    abstract public function total(): int;

    abstract public function payloadOffset(): int;

    abstract public function payloadLimit(): int;

    abstract public function payloadWith(): array;

    abstract public function payloadOrders(): array;

    abstract public function payloadSelect(): array;

    abstract public function getConfigurator(): IndexContract;
}
