<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch;

use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Contracts\MappingContract;

/**
 * Class Index.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
abstract class AbstractIndex implements IndexContract
{
    protected array $settings = [
        'index' => [
            'number_of_shards' => 5,
            'number_of_replicas' => 5,
        ],
        'analysis' => [
            'filter' => [
                // @see https://stackoverflow.com/a/30116612
                'email' => [
                    'type' => 'pattern_capture',
                    'preserver_original' => 1,
                    'patterns' => [
                        '([^@]+)',
                        '(\\p{L}+)',
                        '(\\d+)',
                        '@(.+)',
                        '([^-@]+)',
                    ],
                ],
            ],
            'analyzer' => [
                'email' => [
                    'tokenizer' => 'uax_url_email',
                    'filter' => [
                        'email',
                        'lowercase',
                        'unique',
                    ],
                ],
            ],
        ],
    ];

    protected string $indexName;

    abstract public function mapToIndex($data): array;

    abstract public function getMapping(): MappingContract;

    public function getId($data)
    {
        return $data['id'];
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getName(): string
    {
        return $this->indexName;
    }
}
