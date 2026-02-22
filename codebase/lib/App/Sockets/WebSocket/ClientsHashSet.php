<?php

namespace App\Sockets\WebSocket;

use Bitrix\Sender\Dispatch\Duration;
use Socket;

class ClientsHashSet
{
    private array $clients = [];

    public function add(Socket $socket): void
    {
        $hash = $this->calculateHash($socket);
        $this->clients[$hash] = $socket;
    }

    public function remove(Socket $socket): void
    {
        $hash = $this->calculateHash($socket);
        unset($this->clients[$hash]);
        gc_collect_cycles();
    }

    public function get(string $hash): ?Socket
    {
        return $this->clients[$hash] ?? null;
    }

    public function has(Socket $socket): bool
    {
        $hash = $this->calculateHash($socket);
        return isset($this->clients[$hash]);
    }

    public function calculateHash(Socket $socket): string
    {
        socket_getpeername($socket, $peername, $peerport);
        return sha1($peername . ':' . $peerport);
    }

    public function getAll(): ?array
    {
        return $this->clients;
    }

    public function getCount(): int {
        return sizeof($this->clients);
    }
}