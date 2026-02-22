<?php

namespace App\Sockets\WebSocket\Messages;

class GreetingMessage extends Message
{

    protected string $type = "greeting";

    public function __construct(string $message, ?string $id = null)
    {
        parent::__construct($message, $id);
    }
}
