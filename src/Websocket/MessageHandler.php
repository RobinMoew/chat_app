<?php

namespace App\Websocket;

use App\Service\MessageService;
use App\Service\UserService;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageHandler implements MessageComponentInterface
{
    protected $connections;

    public function __construct()
    {
        $this->connections = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
        $this->container->get('doctrine');
        $querystring = $conn->httpRequest->getUri()->getQuery();
        $query = explode('=', $querystring);
        $user = $this->container->get(UserRepository::class)->findById($query[1]);
        echo "{$user->getUsername()}\n";
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                continue;
            }
            $connection->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()} {$e->getFile()} line:{$e->getLine()}\n";
        $this->connections->detach($conn);
        $conn->close();
    }
}
