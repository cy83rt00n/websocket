<?php

namespace App\Core\DI;

use App\Core\Interfaces\DIAutowireInterface;
use App\Core\Interfaces\DIContainerInterface;
use App\Core\Interfaces\DIRegistratorInterface;
use App\Core\Interfaces\DIResolverInterface;
use Exception;

class DIContainer implements DIContainerInterface
{
    protected DIRegistratorInterface $registrator;
    protected DIResolverInterface $resolver;
    protected DIAutowireInterface $autowire;
    protected array $instances;

    public const SERVICE_NOT_FOUND_EXCEPTION = "Service not found: ";

    public function __construct(DIRegistratorInterface $registrator, DIAutowireInterface $autowire)
    {
        $this->registrator = $registrator;
        $this->autowire = $autowire;
    }

    public function get(string $serviceId): object
    {
        if (!$this->registrator->has($serviceId)) {
            throw new Exception(self::SERVICE_NOT_FOUND_EXCEPTION . $serviceId);
        }

        if ($this->registrator->get($serviceId)['isSingleton'] && isset($this->instances[$serviceId])) {
            return $this->instances[$serviceId];
        }

        $resolver = $this->registrator->get($serviceId)['resolver']->get($serviceId);
        $arguments = $this->registrator->get($serviceId)['arguments'];
        $this->instances[$serviceId] = is_string($resolver)
            ? $this->autowire->wire($this, $resolver, $arguments)
            : $resolver($this)
        ;
        return $this->instances[$serviceId];
    }

    public function has(string $serviceId): bool
    {
        return isset($this->instances[$serviceId]);
    }
}
