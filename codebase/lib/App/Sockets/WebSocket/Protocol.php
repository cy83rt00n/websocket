<?php

namespace App\Sockets\WebSocket;

use Socket;

class Protocol 
{

    /**
     * 
     * Masks a message according to the WebSocket protocol. This method takes a message string as input and returns the masked message that can be sent to clients. The masking is done by adding a header to the message that indicates the length of the message and applying a bitwise XOR operation to the message data with a masking key.
     * 
     * @param string $message
     * @return string 
     */
    public function mask(string $message): string
    {

        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($message);

        if ($length <= 125) {
            $header = pack("CC", $b1, $length);
        } elseif ($length > 125 && $length < 65536) {
            $header = pack("CCn", $b1, 126, $length);
        } elseif ($length >= 65536) {
            $header = pack("CCNN", $b1, 127, $length);
        }

        return $header . $message;
    }

    /**
     * 
     * Unmasks a message according to the WebSocket protocol. This method takes a masked message string as input and returns the original unmasked message. The unmasking is done by extracting the masking key from the message header and applying a bitwise XOR operation to the masked message data with the masking key to retrieve the original message.
     * 
     * @param mixed $message 
     * @return string 
     */
    public function unmask(string $message): string
    {
        if (empty($message[1])) {
            return $message;
        }
        $length = ord($message[1]) & 127;

        if ($length == 126) {
            $masks = substr($message, 4, 4);
            $data = substr($message, 8);
        } elseif ($length == 127) {
            $masks = substr($message, 10, 4);
            $data = substr($message, 14);
        } else {
            $masks = substr($message, 2, 4);
            $data = substr($message, 6);
        }

        $message = "";

        for ($i = 0; $i < strlen($data); $i++) {
            $message .= $data[$i] ^ $masks[$i % 4];
        }

        return $message;
    }

    /**
     * Performs handshaking according to the WebSocket protocol.
     *
     * Initiates the WebSocket handshake process by exchanging and validating
     * the required headers between client and server to establish a WebSocket connection.
     * The method extracts the 'Sec-WebSocket-Key' from the client's request headers, computes the 'Sec-WebSocket-Accept' response header using a specific GUID, and sends the appropriate HTTP response to complete the handshake.
     * 
     * @param mixed $headers
     * @param mixed $host 
     * @param mixed $port 
     * @return string
     */
    public function upgrade(string $headers, string $host, int $port): string  
    {
        preg_match('/^Sec-WebSocket-Key:\s?(?P<security_key>.*)$/m', $headers, $matches);
        if (empty($matches['security_key'])) {
            throw new \Exception("WebSocket handshake failed: 'Sec-WebSocket-Key' header not found in client request");
        }
        
        $security_key = trim($matches['security_key']);
        if (empty($security_key)) {
            throw new \Exception("WebSocket handshake failed: 'Sec-WebSocket-Key' header is empty in client request");
        }
        
        $secWebSocketAccept = base64_encode(pack('H*', sha1($security_key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ws://$host:$port\r\n" .
            "Sec-WebSocket-Accept:$secWebSocketAccept\r\n\r\n";
        return $upgrade;
    }
}