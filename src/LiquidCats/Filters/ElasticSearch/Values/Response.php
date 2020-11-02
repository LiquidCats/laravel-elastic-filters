<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Support\Collection;
use LiquidCats\Filters\AbstractResponse;
use LiquidCats\Filters\ElasticSearch\Utils;
use LiquidCats\Filters\Contracts\IndexContract;

/**
 * Class Response.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Response extends AbstractResponse
{
    public function data(): Collection
    {
        $data = Utils::getData($this->raw);

        return Collection::make($data);
    }

    public function keys(): Collection
    {
        $ids = Utils::mapIds($this->raw);

        return Collection::make($ids);
    }

    public function total(): int
    {
        return Utils::getTotalCount($this->raw);
    }

    public function payloadOffset(): int
    {
        return (int) $this->builder->offset;
    }

    public function payloadLimit(): int
    {
        return (int) $this->builder->limit ?: 15;
    }

    public function payloadWith(): array
    {
        return $this->builder->with ?? [];
    }

    public function payloadOrders(): array
    {
        return $this->builder->orders ?? [];
    }

    public function payloadSelect(): array
    {
        return $this->builder->select ?? ['*'];
    }

    public function getConfigurator(): IndexContract
    {
        return $this->configurator;
    }
}
