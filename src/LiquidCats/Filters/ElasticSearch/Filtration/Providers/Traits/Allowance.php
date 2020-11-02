<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Filtration\Providers\Traits;

/**
 * Class Allowance.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
trait Allowance
{
    protected array $allow = [];

    /**
     * @param array|string $allow
     *
     * @return $this
     */
    public function allow($allow = ['*']): self
    {
        if ('*' === $allow || $allow === ['*']) {
            $this->allow = ['*'];
        }
        if (is_string($allow) && !in_array($allow, $this->allow, true)) {
            $this->allow[] = $allow;
        }
        if (is_array($allow)) {
            $this->allow = array_unique(array_merge($this->allow, $allow));
        }

        return $this;
    }

    protected function isAllAllowed(): bool
    {
        return in_array('*', $this->allow, true);
    }

    protected function isAllowed($value): bool
    {
        return in_array($value, $this->allow, true);
    }
}
