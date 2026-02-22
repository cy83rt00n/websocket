<?php

namespace App\Core\Interfaces;

use Psr\Container\ContainerInterface;

interface DIContainerInterface extends ContainerInterface {
    public function get(string $serviceId): object;
    public function has(string $serviceId): bool;
}