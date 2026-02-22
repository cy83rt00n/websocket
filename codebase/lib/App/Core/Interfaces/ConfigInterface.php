<?php

namespace App\Core\Interfaces;

interface ConfigInterface {
    public function load(string $file): bool;
    public function has(string $key): bool;
    public function get(string $key): mixed;
}