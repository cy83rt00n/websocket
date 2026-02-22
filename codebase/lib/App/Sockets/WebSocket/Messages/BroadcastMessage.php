<?php

namespace App\Sockets\WebSocket\Messages;

class BroadcastMessage extends Message
{
    protected string $type = 'broadcast';

    public function __construct(string $message, ?string $id = null)
    {
        parent::__construct($message, $id);
    }
}

