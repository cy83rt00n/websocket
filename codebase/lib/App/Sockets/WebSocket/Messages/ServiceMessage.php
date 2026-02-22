<?php

namespace App\Sockets\WebSocket\Messages;

use App\Sockets\WebSocket\Interfaces\ServiceMessageInterface;

class ServiceMessage implements ServiceMessageInterface
{
    protected string $type = 'service';
    protected string $command;
    protected array $args;
    protected ?string $id;

    public function __construct(string $command, array $args = [], ?string $id = null)
    {
        $this->command = $command;
        $this->args = $args;
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function json(): string
    {
        return json_encode([
            'type' => $this->getType(),
            'command' => $this->getCommand(),
            'args' => $this->getArgs(),
            'id' => $this->getId(),
        ]);
    }
}

