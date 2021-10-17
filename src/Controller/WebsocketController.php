<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsocketController extends AbstractController
{
    /**
     * @Route("/chat", name="websocket")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository('App:User')->findAll();
        $messages = $entityManager->getRepository('App:Message')->findAll();

        return $this->render('websocket/index.html.twig', [
            'users' => $users,
            'messages' => $messages,
        ]);
    }
}
