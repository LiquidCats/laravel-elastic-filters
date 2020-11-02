<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Console\Traits;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Model\Searchable;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GetModelArguments.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
trait GetModelArguments
{
    protected function getModel(): Model
    {
        $modelClass = trim($this->argument('model'));

        $modelInstance = new $modelClass();

        if (
            !($modelInstance instanceof Model)
            || !in_array(Searchable::class, class_uses_recursive($modelClass), true)
        ) {
            throw new InvalidArgumentException(sprintf(
                'The %s class must extend %s and use the %s trait.',
                $modelClass,
                Model::class,
                Searchable::class
            ));
        }

        return $modelInstance;
    }

    protected function getArguments(): array
    {
        return [
            [
                'model',
                InputArgument::REQUIRED,
                'The model class',
            ],
        ];
    }
}
