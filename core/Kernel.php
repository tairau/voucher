<?php

declare(strict_types = 1);

namespace Core;

use DI\Container;
use DI\ContainerBuilder;

abstract class Kernel
{
    protected readonly Container $container;

    protected readonly string $root;

    public function __construct()
    {
        $this->root = dirname(__DIR__);
        $this->container = (new ContainerBuilder())
            ->addDefinitions($this->root. '/config/di.php')
            ->useAutowiring(true)
            ->useAttributes(true)
            ->build();
    }

    abstract public function run();
}
