<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Model\Searchable;
use LiquidCats\Filters\Console\Traits\GetModelArguments;

/**
 * Class Import.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Import extends Command
{
    use GetModelArguments;

    protected $name = 'search:import';

    protected $description = 'Will start importing process for given model';

    public function handle(): void
    {
        /** @var Model|Searchable $model */
        $model = $this->getModel();
        $builder = $model::query();

        $builder->chunk(500, function (Collection $models) {
            $models->filter
                ->shouldBeSearchable()
                ->searchable()
            ;

            /** @var Model|Searchable $latest */
            $latest = $models->last();
            $key = $latest->getKey();

            $this->line('<comment>Imported ['.get_class($latest).'] models up to ID:</comment> '.$key);
        });

        $this->info('All ['.get_class($model).'] records have been imported.');
    }
}
