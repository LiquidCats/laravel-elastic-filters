<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LiquidCats\Filters\Enum\Slugs;
use Illuminate\Support\Traits\Macroable;
use LiquidCats\Filters\Contracts\BuilderContract;
use LiquidCats\Filters\ElasticSearch\Values\Aggregate;

/**
 * Class Builder.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Builder implements BuilderContract, Slugs
{
    use Macroable;

    /** @var string */
    public string $query = self::NO_QUERY_SIGN;
    /** @var array */
    public array $wheres = [
        'must' => [],
        'must_not' => [],
    ];
    public array $aggregate = [];
    public array $with = [];
    public ?array $select = null;
    public ?array $orders = null;
    public ?string $collapse = null;
    public ?int $offset = null;
    public ?int $limit = null;

    public function query(string $query): BuilderContract
    {
        if (self::NO_QUERY_SIGN !== $query) {
            $this->query = $query;
        }

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html Term query
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * Supported operators are =, &gt;, &lt;, &gt;=, &lt;=, &lt;&gt;
     *
     * @param mixed[] ...$args
     */
    public function where(...$args): BuilderContract
    {
        $numArgs = count($args);
        if (3 === $numArgs) {
            [$field, $operator, $value] = $args;
        } elseif (2 === $numArgs) {
            [$field, $value] = $args;
            $operator = '=';
        } else {
            throw new BadMethodCallException(sprintf('%s method has 3 required params but %d given', __METHOD__, $numArgs));
        }

        switch ($operator) {
            case '>':
                $this->setWhereClause(['range' => [
                    (string) $field => ['gt' => $value],
                ]]);

                break;

            case '<':
                $this->setWhereClause(['range' => [
                    (string) $field => ['lt' => $value],
                ]]);

                break;

            case '>=':
                $this->setWhereClause(['range' => [
                    (string) $field => ['gte' => $value],
                ]]);

                break;

            case '<=':
                $this->setWhereClause(['range' => [
                    (string) $field => ['lte' => $value],
                ]]);

                break;

            case '!=':
            case '<>':
                $this->setWhereClause(['term' => [
                    (string) $field => $value,
                ]], true);

                break;

            case '=':
            default:
                $this->setWhereClause([
                    'term' => [(string) $field => $value],
                ]);

                break;
        }

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html Terms query
     *
     * @return $this
     */
    public function whereIn(string $field, array $value): BuilderContract
    {
        $this->setWhereClause(['terms' => [$field => $value]]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html Terms query
     *
     * @return $this
     */
    public function whereNotIn(string $field, array $value): BuilderContract
    {
        $this->setWhereClause(['terms' => [$field => $value]], true);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * @return $this
     */
    public function whereBetween(string $field, array $value): BuilderContract
    {
        $this->setWhereClause([
            'range' => [
                $field => [
                    'gte' => $value[0],
                    'lte' => $value[1],
                ],
            ],
        ]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * @return $this
     */
    public function whereNotBetween(string $field, array $value): BuilderContract
    {
        $this->setWhereClause([
            'range' => [
                $field => [
                    'gte' => $value[0],
                    'lte' => $value[1],
                ],
            ],
        ], true);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html Exists query
     *
     * @return $this
     */
    public function whereExists(string $field): BuilderContract
    {
        $this->setWhereClause(['exists' => compact('field')]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html Exists query
     *
     * @return $this
     */
    public function whereNotExists(string $field): BuilderContract
    {
        $this->setWhereClause(['exists' => compact('field')], true);

        return $this;
    }

    /**
     * @return $this
     */
    public function orderBy(string $field, string $direction = Slugs::SORT_ASC): BuilderContract
    {
        $this->orders[] = [
            $field => Slugs::SORT_DESC === strtolower($direction)
                ? Slugs::SORT_DESC
                : Slugs::SORT_ASC,
        ];

        return $this;
    }

    /**
     * @return $this
     */
    public function take(int $limit = 15): BuilderContract
    {
        $this->limit = $limit;

        return $this;
    }

    public function from(?int $from): BuilderContract
    {
        $this->offset = $from;

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html Regexp query
     *
     * @param string $flags
     *
     * @return $this
     */
    public function whereRegexp(string $field, string $value, $flags = 'ALL'): BuilderContract
    {
        $this->setWhereClause([
            'regexp' => [
                $field => [
                    'value' => $value,
                    'flags' => $flags,
                ],
            ],
        ]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html Geo distance query
     *
     * @param array|string $value
     * @param int|string   $distance
     *
     * @return $this
     */
    public function whereGeoDistance(string $field, $value, $distance): BuilderContract
    {
        $this->setWhereClause([
            'geo_distance' => [
                'distance' => $distance,
                $field => $value,
            ],
        ]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-bounding-box-query.html Geo bounding box query
     *
     * @return $this
     */
    public function whereGeoBoundingBox(string $field, array $value): BuilderContract
    {
        $this->setWhereClause([
            'geo_bounding_box' => [
                $field => $value,
            ],
        ]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-polygon-query.html Geo polygon query
     *
     * @return $this
     */
    public function whereGeoPolygon(string $field, array $points): BuilderContract
    {
        $this->setWhereClause([
            'geo_polygon' => [
                $field => compact('points'),
            ],
        ]);

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/guide/current/querying-geo-shapes.html Querying Geo Shapes
     *
     * @return $this
     */
    public function whereGeoShape(string $field, array $shape): BuilderContract
    {
        $this->setWhereClause([
            'geo_shape' => [
                $field => compact('shape'),
            ],
        ]);

        return $this;
    }

    /**
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations): BuilderContract
    {
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }
        $this->with = $relations;

        return $this;
    }

    /**
     * @return $this
     */
    public function collapse(string $field): BuilderContract
    {
        $this->collapse = $field;

        return $this;
    }

    /**
     * @param mixed $fields
     *
     * @return $this
     */
    public function select($fields): BuilderContract
    {
        $this->select = array_merge($this->select ?? [], Arr::wrap($fields));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withTrashed(): BuilderContract
    {
        $body = Collection::make(Arr::get($this->wheres, 'must', []));

        $musts = $body
            ->filter(static function ($item) {
                return 0 !== Arr::get($item, 'term.__soft_deleted');
            })
            ->values()
            ->all()
        ;

        Arr::set($this->wheres, 'must', $musts);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function onlyTrashed(): BuilderContract
    {
        return tap($this->withTrashed(), function () {
            $this->setWhereClause(['term' => ['__soft_deleted' => 1]]);
        });
    }

    /**
     * Apply the callback's query changes if the given "value" is true.
     *
     * @param mixed    $value
     * @param callable $callback
     * @param null     $default
     *
     * @return $this
     */
    public function when($value, $callback, $default = null): BuilderContract
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }

        if ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    public function aggregate(Aggregate $aggr): BuilderContract
    {
        $this->aggregate = array_merge($this->aggregate, $aggr->toArray());

        return $this;
    }

    /**
     * Pass the query to a given callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function tap($callback): BuilderContract
    {
        return $this->when(true, $callback);
    }

    /**
     * @param bool $not
     *
     * @return $this
     */
    protected function setWhereClause(array $value, $not = false): BuilderContract
    {
        $clauseKey = $not ? 'must_not' : 'must';
        $clauseValue = array_merge([$value], Arr::get($this->wheres, $clauseKey, []));
        $this->setIfNotEmpty($clauseKey, $clauseValue);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    protected function setIfNotEmpty(string $key, $value): BuilderContract
    {
        if (empty($value)) {
            return $this;
        }
        Arr::set($this->wheres, $key, $value);

        return $this;
    }
}
