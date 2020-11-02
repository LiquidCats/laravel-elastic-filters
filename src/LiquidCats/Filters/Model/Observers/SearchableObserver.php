<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Model\Observers;

use Illuminate\Database\Eloquent\Model;
use LiquidCats\Filters\Model\Searchable;
use LiquidCats\Filters\Model\SoftDeleteDefinition;

/**
 * Class SearchableObserver.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class SearchableObserver
{
    use SoftDeleteDefinition;

    /**
     * The class names that syncing is disabled for.
     */
    protected static array $syncingDisabledFor = [];

    /**
     * Enable syncing for the given class.
     */
    public static function enableSyncingFor(string $class): void
    {
        unset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Disable syncing for the given class.
     */
    public static function disableSyncingFor(string $class): void
    {
        static::$syncingDisabledFor[$class] = true;
    }

    /**
     * Determine if syncing is disabled for the given class or model.
     *
     * @param object|string $class
     */
    public static function syncingDisabledFor($class): bool
    {
        $class = is_object($class) ? get_class($class) : $class;

        return isset(static::$syncingDisabledFor[$class]);
    }

    /**
     * Handle the saved event for the model.
     *
     * @param Model|Searchable $model
     */
    public function saved($model): void
    {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if (!$model->shouldBeSearchable()) {
            $model->unsearchable();

            return;
        }
        $model->searchable();
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param Model|Searchable $model
     */
    public function deleted($model): void
    {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        if (self::usesSoftDelete($model)) {
            $this->saved($model);
        } else {
            $model->unsearchable();
        }
    }

    /**
     * Handle the force deleted event for the model.
     *
     * @param Model|Searchable $model
     */
    public function forceDeleted($model): void
    {
        if (static::syncingDisabledFor($model)) {
            return;
        }

        $model->unsearchable();
    }

    /**
     * Handle the restored event for the model.
     *
     * @param Model|Searchable $model
     */
    public function restored($model): void
    {
        $this->saved($model);
    }
}
