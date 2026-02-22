<?php

namespace App\Core\Interfaces;

interface DIResolverInterface
{
    public function register(string $serviceId, string|callable $resolver): DIResolverInterface;
    public function has(string $serviceId): bool;
    public function get(string $serviceId): string|callable;
}
