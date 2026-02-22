<?php

namespace App\Core\Interfaces;

use App\Core\Interfaces\DIContainerInterface;

interface DIAutowireInterface
{
    public function wire(DIContainerInterface $container, string $resolver, array $forcedArguments = []): ?object;
}
