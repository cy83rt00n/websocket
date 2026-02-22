<?php

namespace App\Sockets\WebSocket\Interfaces;

interface MessageInterface
{
    public function getType(): string;
    public function getMessage(): string;
    public function getId(): ?string;
    public function verify(): bool;
    public function json(): string;
}