<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface IndexContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface IndexContract
{
    public const NUMBER_OF_SHARDS = 5;
    public const NUMBER_OF_REPLICAS = 1;

    /**
     * Should return index mapping contract instance.
     */
    public function getMapping(): MappingContract;

    /**
     * Return index settings array.
     */
    public function getSettings(): array;

    /**
     * Should return index mapping contract instance.
     *
     * @param array|Model|object $data
     *
     * @return mixed
     */
    public function getId($data);

    /**
     * Should return index mapping contract instance.
     *
     * @param array|Model|object $data
     */
    public function mapToIndex($data): array;

    public function getName(): string;
}
