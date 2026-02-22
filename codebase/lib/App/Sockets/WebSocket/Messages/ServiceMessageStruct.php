<?php

namespace App\Sockets\WebSocket\Messages;

class ServiceMessageStruct
{
    public array $struct = [
        'type',
        'command',
        'args',
        'id',
    ];

    public function verify(array $data): bool
    {
        $return = true;
        if (!isset($data['type']) || $data['type'] !== 'service') {
            $return = false;
        }

        if (empty($data['command'])) {
            $return = false;
        }

        if (!isset($data['args'])) {
            $return = false;
        }

        if (empty($data['id'])) {
            $return = false;
        }

        return $return;
    }

}