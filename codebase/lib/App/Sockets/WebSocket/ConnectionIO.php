<?php

namespace App\Sockets\WebSocket;

use App\Sockets\WebSocket\Interfaces\IOInterface;
use App\Sockets\WebSocket\Interfaces\MessageInterface;
use Socket;

class ConnectionIO implements IOInterface
{
    public function open(Socket $socket): Socket|false
    {
        return socket_accept($socket);
    }

    public function read(Socket $socket): string|false
    {
        $buffer = '';
        $bytes_received = @socket_recv($socket, $buffer, 4096, 0);
        if ($bytes_received === false) {
            return false;
        }
        return $buffer;
        // return @socket_read($socket, 4096);
    }

    public function write(Socket $socket, string $message): int|false
    {
        return @socket_write($socket, $message, strlen($message));
    }

    public function close(Socket $socket): void
    {
        @socket_close($socket);
    }
}