# laravel-elastic-filters

Filtration for Laravel with Elastic Search

# Installation

`composer require liquid-cats/laravel-elastic-filters`

`php artisan package:discover`

## Model 

To make everything work use Searchable trait on your model 
than define index configurator with all necessary mappings 
and index name you suppose to connect.

```php
<?php
 
use Illuminate\Tests\Integration\Database\EloquentModelLoadCountTest\BaseModel;
use LiquidCats\Filters\Model\Searchable;

class User extends BaseModel {
    use Searchable;

    public string $indexConfigurator = UserIndex::class;
}
```

```php
<?php
 
use LiquidCats\Filters\Contracts\MappingContract;
use LiquidCats\Filters\ElasticSearch\AbstractIndex;use LiquidCats\Filters\ElasticSearch\Mapping;

class UserIndex extends AbstractIndex {
    protected string $indexName = 'users';

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

## Console

Use provided console commands to create, drop and import 

`php artisan search:import "[model class here]"`

`php artisan search:drop-index "[configurator class here]"`

`php artisan search:create-index "[configurator class here]"`



