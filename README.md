# Laravel Filters

[![Packagist](https://img.shields.io/packagist/v/liquid-cats/laravel-elastic-filters.svg)](https://packagist.org/packages/liquid-cats/laravel-elastic-filters)
[![Packagist](https://img.shields.io/packagist/dt/liquid-cats/laravel-elastic-filters.svg)](https://packagist.org/packages/liquid-cats/laravel-elastic-filters)
![PHP Composer](https://github.com/LiquidCats/laravel-filters/workflows/PHP%20Composer/badge.svg?branch=main)

Faster way to integrate ElasticSearch into your Laravel application

## Contents

* [Features](#features)
* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Index configurator](#index-configurator)
* [Searchable model](#searchable-model)
* [Usage](#usage)
* [Available filters](#available-filters)
* [TODO](#todo)

## Features

* An easy way to [configure](#index-configurator) and [create](#console-commands) an Elasticsearch index. 
* A fully configurable mapping for each [model](#searchable-model).
* Integrated flexible filtration system 
* Fewer dependencies.

## Requirements

The package has been tested in the following configuration:

* PHP version &gt;=7.4
* Laravel Framework version &gt;=8.0
* Elasticsearch version &gt;=7

## Installation

Use composer to install the package:

`composer require liquid-cats/laravel-elastic-filters`

Use discover command to discover package service provider

`php artisan package:discover`

## Configuration

Add to `config/services.php`

```php
return [
    // ...
    'search' => [
        'hosts' => [
            'search:9300' // <= replace with your parameter
        ],
    ]
];
```

## Index configurator

```php
<?php
 
use LiquidCats\Filters\Contracts\MappingContract;
use LiquidCats\Filters\ElasticSearch\AbstractIndex;use LiquidCats\Filters\ElasticSearch\Mapping;

class UserIndex extends AbstractIndex {
    protected string $indexName = 'users';

    public function getId($data)
    {
        return $data->id;
    }

    public function mapToIndex($data) : array
    {
        return [
            'id' => $data->id
        ];
    }
    public function getMapping() : MappingContract
    {
        return Mapping::make()
            ->addInteger('id');
    }
}
```

`AbstractIndex::class` provides some default setting for indexes you can adjust it by modifying `$settings` property

More about index settings you can find in the [index management section](https://www.elastic.co/guide/en/elasticsearch/guide/current/index-management.html) of Elasticsearch documentation.

Note, that every searchable model requires its own index configurator.

> Indices created in Elasticsearch 6.0.0 or later may only contain a single mapping type. Indices created in 5.x with multiple mapping types will continue to function as before in Elasticsearch 6.x. Mapping types will be completely removed in Elasticsearch 7.0.0.

You can find more information [here](https://www.elastic.co/guide/en/elasticsearch/reference/6.x/removal-of-types.html).

## Searchable model 

To make everything work use Searchable trait on your model than define index configurator with all necessary mappings and index name you suppose to connect.

```php
<?php
 
use Illuminate\Tests\Integration\Database\EloquentModelLoadCountTest\BaseModel;
use LiquidCats\Filters\Model\Searchable;

class User extends BaseModel {
    use Searchable;

    public string $indexConfigurator = UserIndex::class;
}
```

## Usage

Once you've created an index configurator, and a searchable model, you are ready to go.

* `php artisan search:import "[model class here]"` - put all data to index for given model
* `php artisan search:drop-index "[configurator class here]"` - remove index
* `php artisan search:create-index "[configurator class here]"` - create index

Also you can apply Eloquent like filters

```php
// set query string
User::search('John')
    // specify columns to select
    ->select(['title', 'price'])
    // filter 
    ->where('color', 'red')
    // sort
    ->orderBy('price', 'asc')
    // collapse by field
    ->collapse('brand')
    // set offset
    ->from(0)
    // set limit
    ->take(10)
    // get results
    ->get();
```

If you only need the number of matches for a query, use the `count` method:

```php
User::search('John') 
    ->count();
```

In addition to standard functionality the package offers you the possibility to filter data in Elasticsearch without specifying a query string:

```php
App\MyModel::search()
    ->where('id', 1)
    ->get();
```

Also use [variety](#available-filters) of `where` conditions:

```php
User::search()
    ->whereRegexp('name.raw', 'A.+')
    ->where('age', '>=', 30)
    ->whereExists('unemployed')
    ->get();
```

## Available filters

You can use different types of filters:

Method | Example | Description
--- | --- | ---
where($field, $value) | where('id', 1) | Checks equality to a simple value.
where($field, $operator, $value) | where('id', '>=', 1) | Filters records according to a given rule. Available operators are: =, <, >, <=, >=, <>.
whereIn($field, $value) | where('id', [1, 2, 3]) | Checks if a value is in a set of values.
whereNotIn($field, $value) | whereNotIn('id', [1, 2, 3]) | Checks if a value isn't in a set of values.
whereBetween($field, $value) | whereBetween('price', [100, 200]) | Checks if a value is in a range.
whereNotBetween($field, $value) | whereNotBetween('price', [100, 200]) | Checks if a value isn't in a range.
whereExists($field) | whereExists('unemployed') | Checks if a value is defined.
whereNotExists($field) | whereNotExists('unemployed') | Checks if a value isn't defined.
whereMatch($field, $value) | whereMatch('tags', 'travel') | Filters records matching exact value. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html) you can find more about syntax.
whereNotMatch($field, $value) | whereNotMatch('tags', 'travel') | Filters records not matching exact value. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html) you can find more about syntax.
whereRegexp($field, $value, $flags = 'ALL') | whereRegexp('name.raw', 'A.+') | Filters records according to a given regular expression. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-syntax) you can find more about syntax.
whereGeoDistance($field, $value, $distance) | whereGeoDistance('location', [-70, 40], '1000m') | Filters records according to given point and distance from it. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html) you can find more about syntax.
whereGeoBoundingBox($field, array $value) | whereGeoBoundingBox('location', ['top_left' =>  [-74.1, 40.73], 'bottom_right' => [-71.12, 40.01]]) | Filters records within given boundings. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-bounding-box-query.html) you can find more about syntax.
whereGeoPolygon($field, array $points) | whereGeoPolygon('location', [[-70, 40],[-80, 30],[-90, 20]]) | Filters records within given polygon. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-polygon-query.html) you can find more about syntax.
whereGeoShape($field, array $shape, $relation = 'INTERSECTS') | whereGeoShape('shape', ['type' => 'circle', 'radius' => '1km', 'coordinates' => [4, 52]], 'WITHIN') | Filters records within given shape. [Here](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html) you can find more about syntax.

In most cases it's better to use raw fields to filter records, i.e. not analyzed fields.

## TODO

* add debugging
* add stubs

