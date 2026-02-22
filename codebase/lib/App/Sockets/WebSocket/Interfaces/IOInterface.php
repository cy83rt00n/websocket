<?php

namespace App\Sockets\WebSocket\Interfaces;

use App\Sockets\WebSocket\Interfaces\MessageInterface;
use Socket;

interface IOInterface
{
    public function open(Socket $socket): Socket|false;
    public function read(Socket $socket): string|false;
    public function write(Socket $socket, string $message): int|false;
    public function close(Socket $socket): void;
}
