<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Contracts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface MappingContract.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
interface MappingContract extends Arrayable
{
    public function add(string $fieldName, array $mapping = []): MappingContract;

    public function fastAdd(string $fieldName, string $type): MappingContract;

    /**
     * @param $fieldName
     */
    public function addObject($fieldName, MappingContract $properties): MappingContract;

    public function addTimestamp(string $fieldName, array $mapping = []): MappingContract;

    public function addDate(string $fieldName, array $mapping = []): MappingContract;

    public function addDateTime(string $fieldName, array $mapping = []): MappingContract;

    public function addLong(string $fieldName): MappingContract;

    public function addDouble(string $fieldName): MappingContract;

    public function addInteger(string $fieldName): MappingContract;

    public function addKeyword(string $fieldName): MappingContract;

    public function addEmailField(string $fieldName): MappingContract;

    public function addBoolean(string $fieldName): MappingContract;

    public function addText(string $fieldName, array $mapping = []): MappingContract;

    public function addSearchableText(string $fieldName, array $mapping = []): MappingContract;

    public function forget(string $field): MappingContract;
}
