<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getMessage($userId)
    {
        $user = $this->entityManager->getRepository('App:User')->find($userId);

        return $user->getMessage();
    }

    public function setMessage($userId, $message)
    {
        $user = $this->entityManager->getRepository('App:User')->find($userId);

        $user->setMessage($message);

        $this->entityManager->flush();
    }
}
