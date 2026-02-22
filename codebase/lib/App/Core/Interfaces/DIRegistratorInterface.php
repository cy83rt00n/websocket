<?php

namespace App\Core\Interfaces;

interface DIRegistratorInterface
{
    public function prototype(string $serviceId, DIResolverInterface $resolver, array $arguments = []);
    public function singleton(string $serviceId, DIResolverInterface $resolver, array $arguments = []);
    public function get(string $serviceId): array;
    public function has(string $serviceId): bool;
}
