<?php

declare(strict_types=1);

namespace LiquidCats\Filters\ElasticSearch\Values;

use Illuminate\Support\Arr;

/**
 * Class Payload.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
class Payload
{
    protected array $payload = [];

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $key, $value): self
    {
        if (null !== $key) {
            Arr::set($this->payload, $key, $value);
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setIfNotEmpty(string $key, $value): self
    {
        if (empty($value)) {
            return $this;
        }

        return $this->set($key, $value);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setIfNotNull(string $key, $value): self
    {
        if (null === $value) {
            return $this;
        }

        return $this->set($key, $value);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->payload, $key);
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function add(string $key, $value): self
    {
        if (null !== $key) {
            $currentValue = Arr::get($this->payload, $key, []);

            if (!is_array($currentValue)) {
                $currentValue = Arr::wrap($currentValue);
            }

            $currentValue[] = $value;

            Arr::set($this->payload, $key, $currentValue);
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function addIfNotEmpty(string $key, $value): self
    {
        if (empty($value)) {
            return $this;
        }

        return $this->add($key, $value);
    }

    /**
     * @param null|string $key
     * @param null|mixed  $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return Arr::get($this->payload, $key, $default);
    }
}
