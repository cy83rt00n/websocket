<?php

namespace App\Sockets\WebSocket;

use Socket;

class ClientDataHashSet {
    private array $clientData = [];

    public function add(Socket $socket, array $data): void
    {
        $hash = $this->calculateHash($socket);
        $this->clientData[$hash] = $data;
    }

    public function remove(Socket $socket): void
    {
        $hash = $this->calculateHash($socket);
        unset($this->clientData[$hash]);
    }

    public function get(Socket $socket): array
    {
        $hash = $this->calculateHash($socket);
        return $this->clientData[$hash] ?? null;
    }

    public function has(Socket $socket): bool
    {
        $hash = $this->calculateHash($socket);
        return isset($this->clientData[$hash]);
    }

    private function calculateHash(Socket $socket): string
    {
        socket_getpeername($socket, $peername, $peerport);
        return sha1($peername . ':' . $peerport);
    }
}