<?php

namespace App\Websocket;

use App\Entity\Message;
use App\Service\MessageService;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageHandler implements MessageComponentInterface
{
    protected $connections;
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->connections = new \SplObjectStorage;
        $this->em = $em;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
        $querystring = $conn->httpRequest->getUri()->getQuery();
        $query = explode('=', $querystring); //? query[1] -> userId
        $user = $this->em->getRepository('App:User')->find($query[1]);
        $user->setIsOnline(true);
        $user->setRessourceId($conn->resourceId);
        $this->em->persist($user);
        $this->em->flush();

        echo "{$user->getUsername()} has connected.\n";
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
        $user = $this->em->getRepository('App:User')->findOneBy(['ressourceId' => $connection->resourceId]);
        $arrayMsg = json_decode($msg, true);
        $message = new Message();
        $message->setMessage($arrayMsg['message'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setIsRead(false)
            ->setSender($user);
        $this->em->persist($message);
        $this->em->flush();
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
        $user = $this->em->getRepository('App:User')->findOneBy(['ressourceId' => $conn->resourceId]);
        $user->setIsOnline(false);
        $user->setRessourceId(null);
        $this->em->persist($user);
        $this->em->flush();
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()} {$e->getFile()} line:{$e->getLine()}\n";
        $this->connections->detach($conn);
        $conn->close();
    }
}
