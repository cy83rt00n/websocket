<?php

namespace App\Core\DI;

use App\Core\Interfaces\DIAutowireInterface;
use App\Core\Interfaces\DIContainerInterface;
use ReflectionClass;

class DIContainerAutowire implements DIAutowireInterface
{

    public function wire(DIContainerInterface $container, string $resolver, array $forcedArguments = []): ?object
    {
        if (!class_exists($resolver)) {
            return null;
        }

        $reflection = new ReflectionClass($resolver);
        $arguments = [];
        if (($constructor = $reflection->getConstructor()) !== null) {
            foreach ($constructor->getParameters() as $param) {
                if (array_key_exists($param->getName(), $forcedArguments)) {
                    $arguments[$param->getName()] = $forcedArguments[$param->getName()];
                    continue;
                }

                $paramClass = $param->getType();

                $arguments[] = $paramClass ? $container->get($paramClass->getName()) : null;
            }
        }

        return $reflection->newInstanceArgs($arguments);
    }
}