<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUser($userId)
    {
        $user = $this->entityManager->getRepository('App:User')->find($userId);

        return $user->getMessage();
    }

    public function setRessourceId($userId, $resourceId)
    {
        $user = $this->entityManager->getRepository('App:User')->find($userId);
        $user->setRessourceId($resourceId);

        $this->entityManager->flush();
    }
}
