<?php

declare(strict_types = 1);

namespace Core;

use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;

class Console extends Kernel
{
    protected readonly array $commandsPatterns;

    public function __construct() {
        parent::__construct();

        $this->commandsPatterns = $this->container->get('commands.patterns');
    }

    /**
     * @throws \DI\DependencyException
     * @throws \Exception
     */
    public function run(): void
    {
        $app = new SymfonyConsole();

        $this->registerCommands($app);

        $app->run();
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function registerCommands(SymfonyConsole $app): void
    {
        foreach ($this->commandsPatterns as $namespace => $pattern) {
            $app->addCommands(
                array_map(function ($file) use ($namespace): Command {
                    $class = $this->getClassNamespace($namespace, $file);

                    return $this->container->get($class);
                }, $this->resolveCommandsFiles($pattern))
            );
        }
    }

    protected function resolveCommandsFiles(string $pattern): array
    {
        return glob($this->root . $pattern);
    }

    protected function getClassNamespace(string $namespace, string $file): string
    {
        return $namespace . pathinfo($file)['filename'];
    }
}


