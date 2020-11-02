<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits;

/**
 * Class Defaults.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
trait Defaults
{
    /** @var mixed */
    protected $defaultValue;
    protected bool $hasDefault = false;

    public function setDefault($value): self
    {
        $this->hasDefault = true;
        $this->defaultValue = $value;

        return $this;
    }

    public function getDefault()
    {
        return $this->defaultValue;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }
}
