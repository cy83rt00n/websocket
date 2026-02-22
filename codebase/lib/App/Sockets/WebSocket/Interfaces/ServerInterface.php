<?php

namespace App\Sockets\WebSocket\Interfaces;

use App\Core\Interfaces\ConfigInterface;

interface ServerInterface
{
    public function __construct(ConfigInterface $config);
    public function start(): void;
    public function stop(): void;
}