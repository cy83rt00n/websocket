<?php

namespace App\Core\Interfaces;

use App\Core\DIContainer;

interface DIService {
    public function register(DIContainer $container): void;
}