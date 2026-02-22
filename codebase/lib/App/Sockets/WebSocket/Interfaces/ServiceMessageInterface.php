<?php

namespace App\Sockets\WebSocket\Interfaces;

interface ServiceMessageInterface
{
    public function getType(): string;
    public function getCommand(): string;
    public function getId(): ?string;
    public function json(): string;
}