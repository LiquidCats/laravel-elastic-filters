<?php

declare(strict_types=1);

namespace LiquidCats\Filters\Console\Traits;

use InvalidArgumentException;
use LiquidCats\Filters\Contracts\IndexContract;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class GetConfiguratorArguments.
 *
 * @author Ilya Shabanov i.s.shabanov@ya.ru
 */
trait GetConfiguratorArguments
{
    /**
     * @throws BindingResolutionException
     */
    protected function getIndexConfigurator(): IndexContract
    {
        /** @var Application $app */
        $app = $this->getLaravel();

        $configuratorClass = trim($this->argument('index-configurator'));
        $configuratorInstance = $app->make($configuratorClass);

        if (!($configuratorInstance instanceof IndexContract)) {
            throw new InvalidArgumentException(sprintf(
                'The class %s must extend %s.',
                $configuratorClass,
                IndexContract::class
            ));
        }

        return $configuratorInstance;
    }

    protected function getArguments(): array
    {
        return [
            [
                'index-configurator',
                InputArgument::REQUIRED,
                'The index configurator class',
            ],
        ];
    }
}
