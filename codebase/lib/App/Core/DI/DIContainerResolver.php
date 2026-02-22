<?php

namespace App\Core\DI;

use App\Core\Interfaces\DIResolverInterface;
use Exception;

class DIContainerResolver implements DIResolverInterface
{
    private array $resolvers;
    private const RESOLVER_NOT_REGISTERED_EXCEPTION = "Service %s resolver not registered";

    public function register(string $serviceId, string|callable $resolver): DIContainerResolver
    {
        $this->resolvers[$serviceId] = $resolver;
        return $this;
    }

    public function has(string $serviceId): bool
    {
        return isset($this->resolvers[$serviceId]);
    }

    public function get(string $serviceId): string|callable
    {
        if (!$this->has($serviceId)) {
            throw new Exception(sprintf(
                self::RESOLVER_NOT_REGISTERED_EXCEPTION,
                $serviceId
            ));
        }
        return $this->resolvers[$serviceId];
    }
}
