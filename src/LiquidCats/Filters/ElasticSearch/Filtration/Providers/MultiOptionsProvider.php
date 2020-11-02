<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers;

use LiquidCats\Filters\Contracts\BuilderContract;

/**
 * Class OptionsProvider.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class MultiOptionsProvider extends SingleOptionProvider
{
    public function provide(BuilderContract $builder, array $filters = []): void
    {
        $value = $filters[$this->slug()] ?? null;
        if (null === $value) {
            return;
        }
        $value = explode(',', $value);
        if (empty($value)) {
            return;
        }
        $value = array_filter($value, function ($v) {
            return array_key_exists($v, $this->map);
        });

        $builder->whereIn($this->field, $value);
    }
}
