<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch;

use Illuminate\Support\Arr;
use LiquidCats\Filters\Contracts\MappingContract;

/**
 * Class Mapping.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Mapping implements MappingContract
{
    public const SEARCHABLE_TEXT_FIELDS = [
        'raw' => [
            'type' => 'keyword',
        ],
        'english' => [
            'type' => 'text',
            'analyzer' => 'english',
        ],
        'french' => [
            'type' => 'text',
            'analyzer' => 'french',
        ],
        'german' => [
            'type' => 'text',
            'analyzer' => 'french',
        ],
        'spanish' => [
            'type' => 'text',
            'analyzer' => 'spanish',
        ],
        'italian' => [
            'type' => 'text',
            'analyzer' => 'italian',
        ],
    ];

    protected array $fields = [];

    public static function make(): self
    {
        return new static();
    }

    public function add(string $fieldName, array $mapping = []): MappingContract
    {
        $this->fields[$fieldName] = $mapping;

        return $this;
    }

    public function fastAdd(string $fieldName, string $type): MappingContract
    {
        return $this->add($fieldName, compact('type'));
    }

    public function addObject($fieldName, MappingContract $properties): MappingContract
    {
        $this->fields[$fieldName] = array_merge(
            $properties->toArray()
        );

        return $this;
    }

    public function addTimestamp(string $fieldName, array $mapping = []): MappingContract
    {
        $mapping = array_merge([
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm:ss',
        ], $mapping);

        return $this->add($fieldName, $mapping);
    }

    public function addDate(string $fieldName, array $mapping = []): MappingContract
    {
        $mapping = array_merge([
            'type' => 'date',
            'format' => 'yyyy-MM-dd',
        ], $mapping);

        return $this->add($fieldName, $mapping);
    }

    public function addDateTime(string $fieldName, array $mapping = []): MappingContract
    {
        $mapping = array_merge([
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm:ss',
        ], $mapping);

        return $this->add($fieldName, $mapping);
    }

    public function addLong(string $fieldName): MappingContract
    {
        return $this->fastAdd($fieldName, 'long');
    }

    public function addDouble(string $fieldName): MappingContract
    {
        return $this->fastAdd($fieldName, 'double');
    }

    public function addInteger(string $fieldName): MappingContract
    {
        return $this->fastAdd($fieldName, 'integer');
    }

    public function addKeyword(string $fieldName): MappingContract
    {
        return $this->fastAdd($fieldName, 'keyword');
    }

    public function addEmailField(string $fieldName): MappingContract
    {
        $this->add($fieldName, [
            'type' => 'string',
            'analyzer' => 'email',
            'fields' => [
                'raw' => [
                    'type' => 'keyword',
                ],
            ],
        ]);

        return $this;
    }

    public function addBoolean(string $fieldName): MappingContract
    {
        return $this->fastAdd($fieldName, 'boolean');
    }

    public function addText(string $fieldName, array $mapping = []): MappingContract
    {
        $mapping = array_merge([
            'type' => 'text',
            'fields' => [
                'raw' => [
                    'type' => 'keyword',
                ],
            ],
        ], $mapping);

        return $this->add($fieldName, $mapping);
    }

    public function addSearchableText(string $fieldName, array $mapping = []): MappingContract
    {
        $mapping = array_merge([
            'type' => 'text',
            'fields' => self::SEARCHABLE_TEXT_FIELDS,
        ], $mapping);

        return $this->add($fieldName, $mapping);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'properties' => $this->fields,
        ];
    }

    public function forget(string $field): MappingContract
    {
        Arr::forget($this->fields, $field);

        return $this;
    }
}
