<?php

namespace App\Sockets\WebSocket\Messages;

use App\Sockets\WebSocket\Interfaces\MessageInterface;

class Message implements MessageInterface
{
    protected string $type = 'message';
    protected string $message;
    protected ?string $id;

    public function __construct(string $message, ?string $id = null)
    {
        $this->message = $message;
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function verify(): bool
    {
        return !empty($this->getMessage()) && !empty($this->getId());
    }

    public function json(): string
    {
        return json_encode([
            'type' => $this->getType(),
            'message' => $this->getMessage(),
            'id' => $this->getId(),
        ]);
    }
}