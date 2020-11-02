<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts;

use Illuminate\Support\Collection;
use LiquidCats\Filters\AbstractResponse;

/**
 * Interface EngineContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface EngineContract
{
    public function updateMapping(IndexContract $configurator): void;

    /**
     * Update the given model in the index.
     */
    public function update(IndexContract $configurator, Collection $items): void;

    /**
     * Remove the given model from the index.
     */
    public function delete(IndexContract $configurator, Collection $ids): void;

    /**
     * Provide search query to elastic.
     */
    public function search(IndexContract $configurator, BuilderContract $builder): AbstractResponse;

    public function count(IndexContract $configurator, BuilderContract $builder): int;
}
