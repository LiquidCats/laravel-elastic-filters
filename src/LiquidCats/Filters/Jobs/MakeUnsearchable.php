<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Model\Searchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Contracts\EngineContract;

/**
 * Class MakeUnsearchable.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class MakeUnsearchable implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    protected Collection $models;
    protected IndexContract $configurator;

    /**
     * MakeSearchable constructor.
     */
    public function __construct(IndexContract $configurator, Collection $models)
    {
        $this->models = $models;
        $this->configurator = $configurator;
    }

    public function handle(EngineContract $engine): void
    {
        if (0 === $this->models->count()) {
            return;
        }
        /** @var Model|Searchable $model */
        $model = $this->models->first();
        $priKey = $model->getKeyName();
        $ids = $this->models->pluck($priKey);

        $engine->delete($this->configurator, $ids);
    }
}
