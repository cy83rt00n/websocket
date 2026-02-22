<?php

namespace App\Sockets\WebSocket;

use App\Core\Interfaces\ConfigInterface;
use App\Sockets\WebSocket\Interfaces\ServerInterface;
use Socket;
use App\Sockets\WebSocket\Protocol;
use App\Sockets\WebSocket\ConnectionIO;
use App\Sockets\WebSocket\ClientsHashSet;
use App\Sockets\WebSocket\Messages\GreetingMessage;
use App\Sockets\WebSocket\Messages\BroadcastMessage;
use App\Sockets\WebSocket\Messages\MessageStruct;
use App\Sockets\WebSocket\Messages\ServiceMessageStruct;

/**
 * 
 * @package App\Sockets\WebSocket
 * 
 * A simple WebSocket server implementation in PHP using sockets. This class listens for incoming WebSocket connections, performs the necessary handshake, and allows clients to send messages to the server, which then broadcasts the messages to all connected clients. The server also sends heartbeat messages to clients every 30 seconds to keep the connection alive.
 * 
 * @todo Refactor this class to use a more robust protocol for communication between the server and clients, such as JSON-RPC or Protobuf, instead of sending raw messages
 * @todo Refactor this class to use a more efficient data structure for storing client sockets and their associated data, such as a hash map or a database, instead of an array
 * @todo Refactor this class to handle client disconnections and errors more gracefully, such as by implementing a retry mechanism or a fallback mechanism, instead of just closing the connection and removing the client from the list of clients
 * @todo Refactor this class to use non-blocking sockets and event loops for better performance and scalability, such as by using the ReactPHP library or the Swoole extension, instead of using blocking sockets and a simple loop
 * @todo Refactor this class to use IO class to echo logs instead of echoing directly to the console, such as by using the Monolog library or the PSR-3 logging standard, instead of using echo statements for logging
 */
class Server implements ServerInterface
{
    private Socket $master_socket;
    private Protocol $protocol;
    private ConnectionIO $io;
    private ClientsHashSet $clients_set;
    private ConfigInterface $config;

    /**
     * 
     * Constructor for the WebSocket server. Initializes the master socket, binds it to the specified host and port, and starts listening for incoming connections.
     * 
     * @param ConfigInterface $config 
     * @return void 
     * @throws mixed 
     */
    public function __construct(ConfigInterface $config)
    {
        $socket_host = $config->get('services.ws.ip') or throw new \Exception("WebSocket host not defined in config");
        $socket_port = $config->get('services.ws.port') or throw new \Exception("WebSocket port not defined in config");
        $this->init($socket_host, $socket_port);
        $this->protocol = new Protocol();
        $this->io = new ConnectionIO();
        $this->clients_set = new ClientsHashSet();
        $this->config = $config;
    }

    public function restart(ConfigInterface $config)
    {
        if ($this->master_socket instanceof Socket) {
            $this->stop();
        }
        $socket_host = $config->get('services.ws.ip') or throw new \Exception("WebSocket host not defined in config");
        $socket_port = $config->get('services.ws.port') or throw new \Exception("WebSocket port not defined in config");
        $this->init($socket_host, $socket_port);
    }

    private function init($host, $port)
    {
        $this->master_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option(
            $this->master_socket,
            SOL_SOCKET,
            SO_REUSEADDR,
            1
        );
        socket_set_option(
            $this->master_socket,
            SOL_SOCKET,
            SO_RCVTIMEO,
            array('sec' => 30, 'usec' => 0)
        );
        socket_set_option(
            $this->master_socket,
            SOL_SOCKET,
            SO_SNDTIMEO,
            array('sec' => 30, 'usec' => 0)
        );
        if (!socket_bind($this->master_socket, $host, $port)) {
            match (socket_last_error($this->master_socket)) {
                98 => call_user_func(function () use ($port) {
                    throw new \Exception('Can\'t bind port ' . $port . '. Port already in use.');
                }),
            };
        }
        socket_listen($this->master_socket);
        if (!socket_set_nonblock($this->master_socket)) {
            throw new \Exception("Failed to set non-blocking mode on master socket");
        }
    }

    private function attach(Socket $client)
    {
        socket_getpeername($client, $client_host, $client_port);
        echo "New client connected: $client_host:$client_port" . PHP_EOL;
        $this->clients_set->add($client);
        $this->io->write(
            $client,
            $this->protocol->upgrade(
                $this->io->read($client),
                $this->config->get('services.ws.host'),
                $this->config->get('services.ws.port')
            )
        );
        $this->io->write(
            $client,
            $this->protocol->mask(
                (new GreetingMessage(
                    'Welcome to the WebSocket server! Your address is ' . $client_host . ':' . $client_port,
                    $this->clients_set->calculateHash($client)
                ))->json()
            )
        );
    }

    /**
     * 
     * Main loop of the WebSocket server. Waits for incoming connections, performs the handshake, and handles communication with connected clients. The loop also sends heartbeat messages to clients every 30 seconds to keep the connection alive.
     * 
     * @return mixed 
     * @todo Refactor this method to use non-blocking sockets and event loops for better performance and scalability
     * @todo Refactor this method to handle client disconnections and errors more gracefully, such as by implementing a retry mechanism or a fallback mechanism, instead of just closing the connection and removing the client from the list of clients
     * @todo Refactor this method to use a more efficient data structure for storing client sockets and their associated data, such as a hash map or a database, instead of an array
     * @todo Refactor this method to use IO class to echo logs instead of echoing directly to the console, such as by using the Monolog library or the PSR-3 logging
     */
    private function loop()
    {
        $null = null;
        while (true) {
            echo "Waiting for connections..." . PHP_EOL;

            $readyToRead = ($this->clients_set->getCount() > 0) ? $this->clients_set->getAll() : [$this->master_socket];

            socket_select($readyToRead, $null, $null, null);

            // echo 'Sockets selected: ' . $selected . PHP_EOL;

            // echo "Ready to read sockets selected:\n";
            // var_dump($readyToRead);

            $client = $this->io->open($this->master_socket);

            // echo "Client accepted:\n";
            // var_dump(gettype($client));

            if ($client instanceof Socket) {
                $this->attach($client);
            } else {
                foreach ($readyToRead as $socket) {
                    if ($socket == $this->master_socket) {
                        continue;
                    }

                    $raw_data = $this->io->read($socket);
                    if ($raw_data === false) {
                        $this->clients_set->remove($socket);
                        $this->io->close($socket);
                        continue;
                    }

                    // $data_size = strlen($raw_data);
                    // echo "Raw data size: $data_size bytes" . PHP_EOL;

                    if (strlen($raw_data) > 0) {
                        $this->process($raw_data);
                    }
                }
            }

            
            foreach ($this->clients_set->getAll() as $socket) {
                if (!$this->isAlive($socket)) {
                    // echo "Client socket with ID '$id' is not alive. Closing connection..." . PHP_EOL;
                    $this->clients_set->remove($socket);
                    $this->io->close($socket);
                }
            }
            echo $this->clients_set->getCount() . " client(s) connected." . PHP_EOL;

            usleep(300000); // Sleep for 300 milliseconds to prevent high CPU usage
        }
    }

    private function process($raw_data)
    {
        // echo "Raw data received from client:\n";
        // var_dump($raw_data);
        $data = json_decode($this->protocol->unmask($raw_data), true) ?? throw new \Exception("Failed to decode JSON data from client");
        echo "Data received from client:\n";
        var_dump($data);
        if((new MessageStruct())->verify($data)) {
            match($data['type']) {
                'broadcast' => call_user_func(function() use ($data) {
                    $message = new BroadcastMessage($data['message'], $data['id']);
                    if (!$message->verify()) {
                        throw new \Exception("Invalid broadcast message received from client");
                    }
                    foreach ($this->clients_set->getAll() as $socket) {
                        $this->io->write($socket, $this->protocol->mask($message->json()));
                    }
                }),
                'message' => call_user_func(function() use ($data) {
                    $message = new BroadcastMessage($data['message'], $data['id']);
                    if (!$message->verify()) {
                        throw new \Exception("Invalid broadcast message received from client");
                    }
                    foreach ($this->clients_set->getAll() as $socket) {
                        $this->io->write($socket, $this->protocol->mask($message->json()));
                    }
                }),
                default => throw new \Exception("Message of unknown type received from client"),
            };
        } 
        elseif ((new ServiceMessageStruct())->verify($data)) {
            match($data['command']) {
                'bye' => call_user_func(function() use ($data) {
                    echo "Client with ID '" . $data['id'] . "' requested to disconnect." . PHP_EOL;
                    $socket = $this->clients_set->get($data['id']);
                    $this->clients_set->remove($socket);
                    $this->io->close($socket);
                }),
                default => throw new \Exception("Unknown command received from client"),
            };
        }
        else {
            throw new \Exception("Malformed message received from client");
        }
    }

    /**
     * 
     * Starts main loop
     * 
     * @return void 
     */
    public function start(): void
    {
        $this->loop();
    }

    /**
     * 
     * Calling SIGTERM for entire script
     * 
     * @return void 
     */
    public function stop(): void
    {
        socket_shutdown($this->master_socket);
        socket_close($this->master_socket);
        unset($this->master_socket);
    }

    private function isAlive(Socket $socket): bool
    {
        $write_check = $this->io->write($socket, '');
        echo "Checking if client socket is alive:\n";

        if ($write_check === false || socket_last_error($socket) === 32 || socket_last_error($socket) === 104) {
            return false;
        }

        return true;
    }
}
