<?php

namespace App\Sockets\WebSocket\Messages;

class MessageStruct
{
    public array $struct = [
        'type',
        'message',
        'id',
    ];

    public function verify(array $data): bool
    {
        foreach ($this->struct as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

}