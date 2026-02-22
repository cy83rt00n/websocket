<?php

namespace App\Core\DI;

use App\Core\Interfaces\DIRegistratorInterface;
use App\Core\Interfaces\DIResolverInterface;
use Exception;

class DIContainerRegistrator implements DIRegistratorInterface
{
    private array $services;
    private const SERVICE_NOT_REGISTERED_EXCEPTION = "Service %s not registered";

    public function prototype(string $serviceId, DIResolverInterface $resolver, array $arguments = [])
    {
        $this->services[$serviceId] = [
            'resolver'    => $resolver,
            'isSingleton' => false,
            'arguments'   => $arguments,
        ];
    }

    public function singleton(string $serviceId, DIResolverInterface $resolver, array $arguments = [])
    {
        $this->services[$serviceId] = [
            'resolver'    => $resolver,
            'isSingleton' => true,
            'arguments'   => $arguments,
        ];
    }

    public function has(string $serviceId): bool
    {
        return isset($this->services[$serviceId]);
    }

    public function get(string $serviceId): array
    {
        if (!$this->has($serviceId)) {
            throw new Exception(sprintf(
                self::SERVICE_NOT_REGISTERED_EXCEPTION,
                $serviceId
            ));
        }
        return $this->services[$serviceId];
    }
}
