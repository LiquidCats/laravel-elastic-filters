<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use LiquidCats\Filters\Contracts\IndexContract;
use LiquidCats\Filters\Contracts\EngineContract;

/**
 * Class MakeSearchable.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class MakeSearchable implements ShouldQueue
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
        $engine->update($this->configurator, $this->models);
    }
}
